<?php

namespace App\Actions\Meetings;

use App\Actions\Calendar\SyncCalendarItemAction;
use App\Models\CalendarEvent;
use App\Repositories\CalendarEventRepository;

class DeleteMeetingAction
{
    public function __construct(
        private CalendarEventRepository $calendarEventRepository,
        private SyncCalendarItemAction $syncCalendarItemAction,
    ) {}

    public function handle(CalendarEvent $event): void
    {
        $this->syncCalendarItemAction->deleteForModel($event);

        $this->calendarEventRepository->delete($event);
    }
}
