<?php

namespace App\Actions\Housing;

use App\Data\Housing\OccupancyData;
use App\Events\OccupancyCreated;
use App\Models\Occupancy;
use App\Repositories\OccupancyRepository;
use App\Repositories\RoomRepository;

class CreateOccupancyAction
{
    public function __construct(
        private OccupancyRepository $occupancyRepository,
        private RoomRepository $roomRepository,
    ) {}

    public function handle(OccupancyData $data): Occupancy
    {
        $room = $this->roomRepository->find($data->roomId);
        if ($room === null) {
            throw new \InvalidArgumentException('Room not found.');
        }

        $overlapping = $this->occupancyRepository->findOverlappingForRoom(
            $data->roomId,
            $data->startsAt,
            $data->endsAt,
            null,
        );

        if ($overlapping->isNotEmpty()) {
            throw new \DomainException(__('housing.occupancy_overlap'));
        }

        $occupancy = $this->occupancyRepository->create($data);

        OccupancyCreated::dispatch($occupancy->id, $occupancy->room_id, $occupancy->employee_id);

        return $occupancy->load(['room.apartment', 'employee.person']);
    }
}
