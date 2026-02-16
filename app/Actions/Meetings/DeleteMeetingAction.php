<?php

namespace App\Actions\Meetings;

use App\Models\CalendarEvent;
use App\Repositories\CalendarEventRepository;

class DeleteMeetingAction
{
    public function __construct(
        private CalendarEventRepository $calendarEventRepository,
    ) {}

    public function handle(CalendarEvent $event): void
    {
        $this->calendarEventRepository->delete($event);
    }
}
