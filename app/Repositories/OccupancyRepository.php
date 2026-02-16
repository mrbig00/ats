<?php

namespace App\Repositories;

use App\Data\Housing\OccupancyData;
use App\Models\Occupancy;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

class OccupancyRepository
{
    public function find(int $id): ?Occupancy
    {
        return Occupancy::query()->with(['room.apartment', 'employee.person'])->find($id);
    }

    /**
     * Find occupancies for a room that overlap with the given date range.
     * Used to enforce no overlapping occupancies.
     *
     * @return Collection<int, Occupancy>
     */
    public function findOverlappingForRoom(int $roomId, CarbonImmutable $startsAt, ?CarbonImmutable $endsAt, ?int $excludeOccupancyId = null): Collection
    {
        $query = Occupancy::query()
            ->where('room_id', $roomId);

        if ($excludeOccupancyId !== null) {
            $query->where('id', '!=', $excludeOccupancyId);
        }

        if ($endsAt === null) {
            $query->where(function ($q) use ($startsAt) {
                $q->whereNull('ends_at')
                    ->where('starts_at', '<=', $startsAt->toDateString());
            });
        } else {
            $query->where(function ($q) use ($startsAt, $endsAt) {
                $q->where(function ($q2) use ($startsAt, $endsAt) {
                    $q2->whereNull('ends_at')
                        ->where('starts_at', '<=', $endsAt->toDateString());
                })->orWhere(function ($q2) use ($startsAt, $endsAt) {
                    $q2->whereNotNull('ends_at')
                        ->where('starts_at', '<=', $endsAt->toDateString())
                        ->where('ends_at', '>=', $startsAt->toDateString());
                });
            });
        }

        return $query->get();
    }

    /**
     * Get active (ongoing) occupancies for an employee.
     *
     * @return Collection<int, Occupancy>
     */
    public function getActiveByEmployeeId(int $employeeId): Collection
    {
        return Occupancy::query()
            ->where('employee_id', $employeeId)
            ->whereNull('ends_at')
            ->with('room.apartment')
            ->get();
    }

    public function create(OccupancyData $data): Occupancy
    {
        return Occupancy::query()->create([
            'room_id' => $data->roomId,
            'employee_id' => $data->employeeId,
            'starts_at' => $data->startsAt->toDateString(),
            'ends_at' => $data->endsAt?->toDateString(),
        ]);
    }

    public function endOccupancy(Occupancy $occupancy, CarbonImmutable $endsAt): Occupancy
    {
        $occupancy->update(['ends_at' => $endsAt->toDateString()]);

        return $occupancy->fresh();
    }

    public function delete(Occupancy $occupancy): void
    {
        $occupancy->delete();
    }
}
