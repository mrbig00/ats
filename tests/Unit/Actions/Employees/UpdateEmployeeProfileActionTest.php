<?php

use App\Actions\Employees\UpdateEmployeeProfileAction;
use App\Data\Employees\UpdateEmployeeProfileData;
use App\Enums\GermanLanguageLevel;
use App\Models\Employee;

test('update employee profile action persists allowed fields', function () {
    $employee = Employee::factory()->create([
        'nationality' => null,
        'german_level' => null,
    ]);

    $data = new UpdateEmployeeProfileData([
        'nationality' => 'AT',
        'german_level' => GermanLanguageLevel::B1->value,
        'has_own_car' => false,
        'housing_needed' => true,
        'driving_license_category' => 'BE',
        'available_from' => '2026-08-01',
    ]);

    $updated = app(UpdateEmployeeProfileAction::class)->handle($employee, $data);

    expect($updated->nationality)->toBe('AT')
        ->and($updated->german_level)->toBe(GermanLanguageLevel::B1)
        ->and($updated->has_own_car)->toBeFalse()
        ->and($updated->housing_needed)->toBeTrue()
        ->and($updated->driving_license_category)->toBe('BE')
        ->and($updated->available_from?->format('Y-m-d'))->toBe('2026-08-01');
});

test('update employee profile action is a no-op when patch is empty', function () {
    $employee = Employee::factory()->create(['nationality' => 'DE']);

    $updated = app(UpdateEmployeeProfileAction::class)->handle($employee, new UpdateEmployeeProfileData([]));

    expect($updated->nationality)->toBe('DE');
});
