<?php

namespace App\Livewire\Housing;

use App\Actions\Housing\UpdateRoomAction;
use App\Data\Housing\RoomData;
use App\Models\Room;
use Livewire\Component;

class EditRoom extends Component
{
    public Room $room;

    public string $name = '';

    public string $notes = '';

    public function mount(Room $room): void
    {
        $this->room = $room->load('apartment');
        $this->authorize('update', $this->room);
        $this->name = $room->name;
        $this->notes = $room->notes ?? '';
    }

    public function save(): mixed
    {
        $this->authorize('update', $this->room);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ], [], [
            'name' => __('housing.room_name'),
            'notes' => __('housing.notes'),
        ]);

        $data = new RoomData(
            apartmentId: $this->room->apartment_id,
            name: $validated['name'],
            notes: $validated['notes'] !== '' ? $validated['notes'] : null,
        );

        app(UpdateRoomAction::class)->handle($this->room, $data);

        $this->dispatch('notify', __('housing.room_updated'));

        return $this->redirect(route('housing.apartments.show', $this->room->apartment), navigate: true);
    }

    public function render()
    {
        return view('livewire.housing.edit-room')->layout('layouts.app', ['title' => __('housing.edit_room')]);
    }
}
