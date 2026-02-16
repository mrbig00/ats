<?php

namespace App\Actions\Meetings;

use App\Data\Meetings\MeetingData;
use App\Events\MeetingScheduled;
use App\Models\CalendarEvent;
use App\Repositories\CalendarEventRepository;

class CreateInternalMeetingAction
{
    public function __construct(
        private CalendarEventRepository $calendarEventRepository,
    ) {}

    public function handle(MeetingData $data): CalendarEvent
    {
        $event = $this->calendarEventRepository->createInternalMeeting($data);

        MeetingScheduled::dispatch($event->id);

        return $event;
    }
}
