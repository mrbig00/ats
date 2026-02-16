<?php

namespace App\Livewire\Housing;

use App\Actions\Housing\CreateApartmentAction;
use App\Data\Housing\ApartmentData;
use Livewire\Component;

class CreateApartment extends Component
{
    public string $name = '';

    public string $address = '';

    public string $notes = '';

    public function mount(): void
    {
        $this->authorize('create', \App\Models\Apartment::class);
    }

    public function save(): mixed
    {
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

        $apartment = app(CreateApartmentAction::class)->handle($data);

        $this->dispatch('notify', __('housing.apartment_created'));

        return $this->redirect(route('housing.apartments.show', $apartment), navigate: true);
    }

    public function render()
    {
        return view('livewire.housing.create-apartment')->layout('layouts.app', ['title' => __('housing.create_apartment')]);
    }
}
