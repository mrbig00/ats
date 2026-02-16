<?php

use App\Actions\Employees\CreateContractAction;
use App\Data\Employees\ContractData;
use Carbon\CarbonImmutable;
use Database\Factories\EmployeeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('create contract action persists contract', function () {
    $employee = EmployeeFactory::new()->create();
    $data = new ContractData(
        employeeId: $employee->id,
        type: 'Permanent',
        startsAt: CarbonImmutable::parse('2024-01-01'),
        endsAt: CarbonImmutable::parse('2025-12-31'),
        notes: 'Initial contract',
    );

    $action = app(CreateContractAction::class);
    $contract = $action->handle($data);

    expect($contract->employee_id)->toBe($employee->id)
        ->and($contract->type)->toBe('Permanent')
        ->and($contract->starts_at->toDateString())->toBe('2024-01-01')
        ->and($contract->ends_at->toDateString())->toBe('2025-12-31')
        ->and($contract->notes)->toBe('Initial contract');
});
