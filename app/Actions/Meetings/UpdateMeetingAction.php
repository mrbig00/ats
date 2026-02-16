<?php

namespace App\Actions\Meetings;

use App\Data\Meetings\MeetingData;
use App\Models\CalendarEvent;
use App\Repositories\CalendarEventRepository;

class UpdateMeetingAction
{
    public function __construct(
        private CalendarEventRepository $calendarEventRepository,
    ) {}

    public function handle(CalendarEvent $event, MeetingData $data): CalendarEvent
    {
        return $this->calendarEventRepository->update($event, $data);
    }
}
