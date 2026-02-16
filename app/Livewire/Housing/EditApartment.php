<?php

namespace App\Livewire\Housing;

use App\Actions\Housing\UpdateApartmentAction;
use App\Data\Housing\ApartmentData;
use App\Models\Apartment;
use Livewire\Component;

class EditApartment extends Component
{
    public Apartment $apartment;

    public string $name = '';

    public string $address = '';

    public string $notes = '';

    public function mount(Apartment $apartment): void
    {
        $this->apartment = $apartment;
        $this->authorize('update', $this->apartment);
        $this->name = $apartment->name;
        $this->address = $apartment->address ?? '';
        $this->notes = $apartment->notes ?? '';
    }

    public function save(): mixed
    {
        $this->authorize('update', $this->apartment);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ], [], [
            'name' => __('housing.apartment_name'),
            'address' => __('housing.address'),
            'notes' => __('housing.notes'),
        ]);

        $data = new ApartmentData(
            name: $validated['name'],
            address: $validated['address'] !== '' ? $validated['address'] : null,
            notes: $validated['notes'] !== '' ? $validated['notes'] : null,
        );

        app(UpdateApartmentAction::class)->handle($this->apartment, $data);

        $this->dispatch('notify', __('housing.apartment_updated'));

        return $this->redirect(route('housing.apartments.show', $this->apartment), navigate: true);
    }

    public function render()
    {
        return view('livewire.housing.edit-apartment')->layout('layouts.app', ['title' => __('housing.edit_apartment')]);
    }
}
