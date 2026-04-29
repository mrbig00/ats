<?php

namespace App\Repositories;

use App\Data\Housing\RoomData;
use App\Models\Room;
use Carbon\CarbonImmutable;

class RoomRepository
{
    /**
     * Count rooms that are currently free (no active occupancy: no occupancy with ends_at null and starts_at <= today).
     */
    public function countFreeRooms(): int
    {
        $today = CarbonImmutable::today()->toDateString();

        return Room::query()
            ->whereDoesntHave('occupancies', function ($q) use ($today) {
                $q->whereNull('ends_at')->where('starts_at', '<=', $today);
            })
            ->count();
    }

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

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Room>
     */
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Room::query()->orderBy('apartment_id')->orderBy('name')->get();
    }

    /**
     * @param list<int> $ids
     * @return \Illuminate\Database\Eloquent\Collection<int, Room>
     */
    public function findManyByIds(array $ids): \Illuminate\Database\Eloquent\Collection
    {
        if ($ids === []) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        return Room::query()->whereIn('id', $ids)->get();
    }

    /**
     * @param array{apartment_id:int,name:string,notes:?string} $attributes
     */
    public function createFromCsv(array $attributes): Room
    {
        return Room::query()->create($attributes);
    }

    /**
     * @param array{apartment_id:int,name:string,notes:?string} $attributes
     */
    public function updateFromCsv(Room $room, array $attributes): Room
    {
        $room->update($attributes);

        return $room->fresh() ?? $room;
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
