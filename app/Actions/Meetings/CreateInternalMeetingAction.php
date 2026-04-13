<?php

namespace App\Actions\Meetings;

use App\Actions\Calendar\SyncCalendarItemAction;
use App\Data\Meetings\MeetingData;
use App\Events\MeetingScheduled;
use App\Models\CalendarEvent;
use App\Repositories\CalendarEventRepository;

class CreateInternalMeetingAction
{
    public function __construct(
        private CalendarEventRepository $calendarEventRepository,
        private SyncCalendarItemAction $syncCalendarItemAction,
    ) {}

    public function handle(MeetingData $data): CalendarEvent
    {
        $event = $this->calendarEventRepository->createInternalMeeting($data);

        $this->syncCalendarItemAction->syncFromModel($event);

        MeetingScheduled::dispatch($event->id);

        return $event;
    }
}
