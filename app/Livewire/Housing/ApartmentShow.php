<?php

namespace App\Livewire\Housing;

use App\Models\Apartment;
use App\Repositories\ApartmentRepository;
use App\Repositories\RoomRepository;
use Livewire\Component;

class ApartmentShow extends Component
{
    public Apartment $apartment;

    protected $listeners = ['occupancy-ended' => 'refreshRooms'];

    public function mount(Apartment $apartment, ApartmentRepository $apartmentRepository): void
    {
        $this->apartment = $apartmentRepository->find($apartment->id) ?? $apartment->load(['rooms.occupancies' => fn ($q) => $q->with('employee.person')->orderByDesc('starts_at')]);
        $this->authorize('view', $this->apartment);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Room>
     */
    public function getRoomsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return app(RoomRepository::class)->getByApartmentId($this->apartment->id);
    }

    public function refreshRooms(): void
    {
        $this->apartment = $this->apartment->fresh();
    }

    public function openEndOccupancyModal(int $occupancyId): void
    {
        $this->dispatch('openEndOccupancyModal', ['occupancyId' => $occupancyId])->to(EndOccupancyModal::class);
    }

    public function render()
    {
        return view('livewire.housing.apartment-show', [
            'rooms' => $this->rooms,
        ])->layout('layouts.app', ['title' => $this->apartment->name]);
    }
}
