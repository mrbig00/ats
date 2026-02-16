<?php

use App\Data\Employees\TerminateEmployeeData;
use App\Events\EmployeeTerminated;
use App\Models\CalendarEvent;
use App\Models\Employee;
use App\Services\TerminateEmployeeWorkflowService;
use Carbon\CarbonImmutable;
use Database\Factories\EmployeeFactory;
use Database\Factories\OccupancyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

test('terminate workflow updates employee status and exit date creates exit event ends occupancies and dispatches event', function () {
    Event::fake([EmployeeTerminated::class]);
    $employee = EmployeeFactory::new()->create([
        'status' => Employee::STATUS_ACTIVE,
        'entry_date' => '2024-01-15',
        'exit_date' => null,
    ]);
    $occupancy = OccupancyFactory::new()->create([
        'employee_id' => $employee->id,
        'starts_at' => '2024-06-01',
        'ends_at' => null,
    ]);
    $exitDate = CarbonImmutable::parse('2025-03-31');

    $service = app(TerminateEmployeeWorkflowService::class);
    $result = $service->handle(new TerminateEmployeeData(
        employeeId: $employee->id,
        exitDate: $exitDate,
        status: Employee::STATUS_LEFT,
    ));

    expect($result->status)->toBe(Employee::STATUS_LEFT)
        ->and($result->exit_date->toDateString())->toBe('2025-03-31')
        ->and(CalendarEvent::query()->where('type', CalendarEvent::TYPE_EXIT_DATE)->count())->toBe(1)
        ->and($occupancy->fresh()->ends_at->toDateString())->toBe('2025-03-31');

    Event::assertDispatched(EmployeeTerminated::class, function (EmployeeTerminated $e) use ($employee) {
        return $e->employeeId === $employee->id && $e->personId === $employee->person_id && $e->status === Employee::STATUS_LEFT;
    });
});

test('terminate workflow throws when employee not active', function () {
    $employee = EmployeeFactory::new()->create(['status' => Employee::STATUS_LEFT]);
    $service = app(TerminateEmployeeWorkflowService::class);

    $service->handle(new TerminateEmployeeData(
        employeeId: $employee->id,
        exitDate: CarbonImmutable::parse('2025-03-31'),
        status: Employee::STATUS_LEFT,
    ));
})->throws(\DomainException::class);
