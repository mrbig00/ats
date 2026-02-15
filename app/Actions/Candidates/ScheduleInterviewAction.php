<?php

namespace App\Actions\Candidates;

use App\Data\Candidates\InterviewData;
use App\Events\InterviewScheduled;
use App\Models\CalendarEvent;
use App\Repositories\CalendarEventRepository;

class ScheduleInterviewAction
{
    public function __construct(
        private CalendarEventRepository $calendarEventRepository,
    ) {}

    public function handle(InterviewData $data): CalendarEvent
    {
        $event = $this->calendarEventRepository->createInterview($data);

        InterviewScheduled::dispatch($data->candidateId, $event->id);

        return $event->load('candidate');
    }
}
