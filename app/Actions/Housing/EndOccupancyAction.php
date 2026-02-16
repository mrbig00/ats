<?php

namespace App\Actions\Housing;

use App\Events\OccupancyEnded;
use App\Models\Occupancy;
use App\Repositories\CalendarEventRepository;
use App\Repositories\OccupancyRepository;
use Carbon\CarbonImmutable;

class EndOccupancyAction
{
    public function __construct(
        private OccupancyRepository $occupancyRepository,
        private CalendarEventRepository $calendarEventRepository,
    ) {}

    public function handle(Occupancy $occupancy, CarbonImmutable $endsAt): Occupancy
    {
        if ($occupancy->starts_at->gt($endsAt)) {
            throw new \DomainException(__('housing.ends_at_before_starts'));
        }

        if ($occupancy->ends_at !== null) {
            throw new \DomainException(__('housing.occupancy_already_ended'));
        }

        $occupancy = $this->occupancyRepository->endOccupancy($occupancy, $endsAt);
        $occupancy->load(['room', 'employee.person']);

        $title = __('housing.room_free_from', [
            'room' => $occupancy->room->name,
            'date' => $endsAt->isoFormat('L'),
        ]);
        $this->calendarEventRepository->createRoomFreeEvent(
            $title,
            $occupancy->room_id,
            $endsAt,
        );

        OccupancyEnded::dispatch($occupancy->id, $occupancy->room_id, $occupancy->employee_id);

        return $occupancy;
    }
}
