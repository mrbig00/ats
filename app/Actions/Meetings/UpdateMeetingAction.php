<?php

namespace App\Actions\Meetings;

use App\Actions\Calendar\SyncCalendarItemAction;
use App\Data\Meetings\MeetingData;
use App\Models\CalendarEvent;
use App\Repositories\CalendarEventRepository;

class UpdateMeetingAction
{
    public function __construct(
        private CalendarEventRepository $calendarEventRepository,
        private SyncCalendarItemAction $syncCalendarItemAction,
    ) {}

    public function handle(CalendarEvent $event, MeetingData $data): CalendarEvent
    {
        $event = $this->calendarEventRepository->update($event, $data);

        $this->syncCalendarItemAction->syncFromModel($event);

        return $event;
    }
}
