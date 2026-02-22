<?php

namespace App\Listeners;

use App\Events\CandidateCreated;
use App\Events\CandidateHired;
use App\Events\CandidateStageChanged;
use App\Events\EmployeeTerminated;
use App\Events\InterviewScheduled;
use App\Events\MeetingScheduled;
use App\Events\TaskCreated;
use App\Models\ActivityEvent;
use App\Repositories\ActivityEventRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;

class LogActivityEventListeners
{
    public function __construct(
        private ActivityEventRepository $activityEventRepository,
    ) {}

    public function handleCandidateCreated(CandidateCreated $event): void
    {
        $this->activityEventRepository->log(
            ActivityEvent::TYPE_CANDIDATE_CREATED,
            CarbonImmutable::now(),
            'candidate',
            $event->candidateId,
            ['person_id' => $event->personId, 'position_id' => $event->positionId],
            Auth::id()
        );
    }

    public function handleCandidateStageChanged(CandidateStageChanged $event): void
    {
        $this->activityEventRepository->log(
            ActivityEvent::TYPE_CANDIDATE_STAGE_CHANGED,
            CarbonImmutable::now(),
            'candidate',
            $event->candidateId,
            ['previous_stage_id' => $event->previousStageId, 'new_stage_id' => $event->newStageId],
            Auth::id()
        );
    }

    public function handleCandidateHired(CandidateHired $event): void
    {
        $this->activityEventRepository->log(
            ActivityEvent::TYPE_EMPLOYEE_HIRED,
            CarbonImmutable::now(),
            'employee',
            $event->employeeId,
            ['candidate_id' => $event->candidateId, 'person_id' => $event->personId],
            Auth::id()
        );
    }

    public function handleEmployeeTerminated(EmployeeTerminated $event): void
    {
        $this->activityEventRepository->log(
            ActivityEvent::TYPE_EMPLOYEE_TERMINATED,
            CarbonImmutable::now(),
            'employee',
            $event->employeeId,
            ['person_id' => $event->personId, 'status' => $event->status, 'exit_date' => $event->exitDate],
            Auth::id()
        );
    }

    public function handleMeetingScheduled(MeetingScheduled $event): void
    {
        $this->activityEventRepository->log(
            ActivityEvent::TYPE_MEETING_SCHEDULED,
            CarbonImmutable::now(),
            'calendar_event',
            $event->calendarEventId,
            null,
            Auth::id()
        );
    }

    public function handleInterviewScheduled(InterviewScheduled $event): void
    {
        $this->activityEventRepository->log(
            ActivityEvent::TYPE_MEETING_SCHEDULED,
            CarbonImmutable::now(),
            'calendar_event',
            $event->calendarEventId,
            ['candidate_id' => $event->candidateId],
            Auth::id()
        );
    }

    public function handleTaskCreated(TaskCreated $event): void
    {
        $this->activityEventRepository->log(
            ActivityEvent::TYPE_TASK_CREATED,
            CarbonImmutable::now(),
            'task',
            $event->taskId,
            ['user_id' => $event->userId],
            $event->userId
        );
    }
}
