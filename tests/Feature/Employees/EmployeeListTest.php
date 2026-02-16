<?php

use App\Models\Employee;
use App\Models\User;
use Database\Factories\EmployeeFactory;

test('guests cannot view employees list', function () {
    $response = $this->get(route('employees.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can view employees list', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('employees.index'));
    $response->assertOk();
});

test('employees list shows employees when present', function () {
    $user = User::factory()->create();
    $employee = EmployeeFactory::new()->create();
    $this->actingAs($user);

    $response = $this->get(route('employees.index'));
    $response->assertOk();
    $response->assertSee($employee->person->fullName());
});
