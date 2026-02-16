<?php

use App\Models\Employee;
use App\Models\User;
use Database\Factories\EmployeeFactory;

test('guests cannot view employee show', function () {
    $employee = EmployeeFactory::new()->create();
    $response = $this->get(route('employees.show', $employee));
    $response->assertRedirect(route('login'));
});

test('authenticated users can view employee show', function () {
    $user = User::factory()->create();
    $employee = EmployeeFactory::new()->create();
    $this->actingAs($user);

    $response = $this->get(route('employees.show', $employee));
    $response->assertOk();
    $response->assertSee($employee->person->fullName());
});
