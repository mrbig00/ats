<?php

namespace App\Services;

use App\Actions\Calendar\SyncCalendarItemAction;
use App\Data\Candidates\ConvertCandidateToEmployeeData;
use App\Data\Employees\UpdateEmployeeProfileData;
use App\Events\CandidateHired;
use App\Models\Candidate;
use App\Models\Employee;
use App\Repositories\CalendarEventRepository;
use App\Repositories\CandidateRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\PipelineStageRepository;
use Illuminate\Support\Facades\DB;

class HireCandidateWorkflowService
{
    public function __construct(
        private CandidateRepository $candidateRepository,
        private EmployeeRepository $employeeRepository,
        private PipelineStageRepository $pipelineStageRepository,
        private CalendarEventRepository $calendarEventRepository,
        private SyncCalendarItemAction $syncCalendarItemAction,
    ) {}

    public function handle(ConvertCandidateToEmployeeData $data): Employee
    {
        $hiredStage = $this->pipelineStageRepository->findByKey('hired');
        if ($hiredStage === null) {
            throw new \DomainException('Pipeline stage "hired" is not configured.');
        }

        return DB::transaction(function () use ($data, $hiredStage) {
            $candidate = $this->candidateRepository->find($data->candidateId);
            if ($candidate === null) {
                throw new \InvalidArgumentException('Candidate not found.');
            }

            if ($this->employeeRepository->findByPersonId($candidate->person_id) !== null) {
                throw new \DomainException('Person is already an employee.');
            }

            $employee = $this->employeeRepository->create(
                $candidate->person_id,
                $data->entryDate,
            );

            $profileAttributes = [
                'nationality' => $candidate->nationality,
                'driving_license_category' => $candidate->driving_license_category,
                'has_own_car' => $candidate->has_own_car,
                'german_level' => $candidate->german_level?->value,
                'available_from' => $candidate->available_from?->toDateString(),
                'housing_needed' => $candidate->housing_needed,
            ];
            if (array_filter($profileAttributes, static fn ($v) => $v !== null) !== []) {
                $this->employeeRepository->applyProfilePatch(
                    $employee,
                    new UpdateEmployeeProfileData($profileAttributes),
                );
                $employee->refresh();
            }

            $this->candidateRepository->updateStage($candidate, $hiredStage->id);

            $title = __('calendar.entry_date_for', ['name' => $candidate->person->fullName()]);
            $entryEvent = $this->calendarEventRepository->createEntryDateEvent($title, $data->entryDate);
            $this->syncCalendarItemAction->syncFromModel($entryEvent);

            CandidateHired::dispatch($candidate->id, $employee->id, $candidate->person_id);

            return $employee->load('person');
        });
    }
}
