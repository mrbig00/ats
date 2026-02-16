<?php

namespace App\Livewire\Housing;

use App\Actions\Housing\CreateRoomAction;
use App\Data\Housing\RoomData;
use App\Models\Apartment;
use Livewire\Component;

class CreateRoom extends Component
{
    public Apartment $apartment;

    public string $name = '';

    public string $notes = '';

    public function mount(Apartment $apartment): void
    {
        $this->apartment = $apartment;
        $this->authorize('create', \App\Models\Room::class);
    }

    public function save(): mixed
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ], [], [
            'name' => __('housing.room_name'),
            'notes' => __('housing.notes'),
        ]);

        $data = new RoomData(
            apartmentId: $this->apartment->id,
            name: $validated['name'],
            notes: $validated['notes'] !== '' ? $validated['notes'] : null,
        );

        $room = app(CreateRoomAction::class)->handle($data);

        $this->dispatch('notify', __('housing.room_created'));

        return $this->redirect(route('housing.apartments.show', $this->apartment), navigate: true);
    }

    public function render()
    {
        return view('livewire.housing.create-room')->layout('layouts.app', ['title' => __('housing.create_room')]);
    }
}
