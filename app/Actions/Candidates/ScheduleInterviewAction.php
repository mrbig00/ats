<?php

namespace App\Actions\Candidates;

use App\Actions\Calendar\SyncCalendarItemAction;
use App\Data\Candidates\InterviewData;
use App\Events\InterviewScheduled;
use App\Models\CalendarEvent;
use App\Repositories\CalendarEventRepository;

class ScheduleInterviewAction
{
    public function __construct(
        private CalendarEventRepository $calendarEventRepository,
        private SyncCalendarItemAction $syncCalendarItemAction,
    ) {}

    public function handle(InterviewData $data): CalendarEvent
    {
        $event = $this->calendarEventRepository->createInterview($data);

        $this->syncCalendarItemAction->syncFromModel($event);

        InterviewScheduled::dispatch($data->candidateId, $event->id);

        return $event->load('candidate');
    }
}
