<?php

namespace App\Services;

use App\Actions\Housing\EndOccupancyAction;
use App\Data\Employees\TerminateEmployeeData;
use App\Events\EmployeeTerminated;
use App\Models\Employee;
use App\Repositories\CalendarEventRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\OccupancyRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class TerminateEmployeeWorkflowService
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
        private CalendarEventRepository $calendarEventRepository,
        private OccupancyRepository $occupancyRepository,
        private EndOccupancyAction $endOccupancyAction,
    ) {}

    public function handle(TerminateEmployeeData $data): Employee
    {
        if ($data->status !== Employee::STATUS_LEAVING && $data->status !== Employee::STATUS_LEFT) {
            throw new \InvalidArgumentException('Status must be leaving or left.');
        }

        $employee = $this->employeeRepository->find($data->employeeId);
        if ($employee === null) {
            throw new \InvalidArgumentException('Employee not found.');
        }

        if ($employee->status !== Employee::STATUS_ACTIVE) {
            throw new \DomainException(__('employee.already_terminated'));
        }

        return DB::transaction(function () use ($data, $employee) {
            $employee = $this->employeeRepository->updateStatus($employee, $data->status, $data->exitDate);
            $employee->load('person');

            $title = __('calendar.exit_date_for', ['name' => $employee->person->fullName()]);
            $this->calendarEventRepository->createExitDateEvent($title, $data->exitDate);

            $activeOccupancies = $this->occupancyRepository->getActiveByEmployeeId($employee->id);
            $exitDateCarbon = CarbonImmutable::parse($data->exitDate->toDateString());
            foreach ($activeOccupancies as $occupancy) {
                $this->endOccupancyAction->handle($occupancy, $exitDateCarbon);
            }

            EmployeeTerminated::dispatch(
                $employee->id,
                $employee->person_id,
                $data->status,
                $data->exitDate->toDateString(),
            );

            return $employee;
        });
    }
}
