<?php

namespace App\Repositories;

use App\Data\Housing\RoomData;
use App\Models\Room;

class RoomRepository
{
    public function find(int $id): ?Room
    {
        return Room::query()->with(['apartment', 'occupancies.employee.person'])->find($id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Room>
     */
    public function getByApartmentId(int $apartmentId): \Illuminate\Database\Eloquent\Collection
    {
        return Room::query()
            ->where('apartment_id', $apartmentId)
            ->with(['occupancies' => fn ($q) => $q->with('employee.person')->orderByDesc('starts_at')])
            ->orderBy('name')
            ->get();
    }

    public function create(RoomData $data): Room
    {
        return Room::query()->create([
            'apartment_id' => $data->apartmentId,
            'name' => $data->name,
            'notes' => $data->notes,
        ]);
    }

    public function update(Room $room, RoomData $data): Room
    {
        $room->update([
            'apartment_id' => $data->apartmentId,
            'name' => $data->name,
            'notes' => $data->notes,
        ]);

        return $room->fresh();
    }

    public function delete(Room $room): void
    {
        $room->delete();
    }
}
