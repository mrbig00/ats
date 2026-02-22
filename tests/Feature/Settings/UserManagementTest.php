<?php

use App\Enums\Role;
use App\Models\User;

test('guests cannot access user management', function () {
    $response = $this->get(route('settings.users.index'));
    $response->assertRedirect(route('login'));
});

test('viewer cannot access user list', function () {
    $user = User::factory()->viewer()->create();
    $this->actingAs($user);

    $response = $this->get(route('settings.users.index'));
    $response->assertForbidden();
});

test('hr cannot access user list', function () {
    $user = User::factory()->hr()->create();
    $this->actingAs($user);

    $response = $this->get(route('settings.users.index'));
    $response->assertForbidden();
});

test('admin can access user list', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $response = $this->get(route('settings.users.index'));
    $response->assertOk();
});

test('admin can create user', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $response = $this->get(route('settings.users.create'));
    $response->assertOk();
});

test('viewer cannot create user', function () {
    $user = User::factory()->viewer()->create();
    $this->actingAs($user);

    $response = $this->get(route('settings.users.create'));
    $response->assertForbidden();
});

test('role enum labels use translation key', function () {
    expect(Role::Admin->label())->not->toBeEmpty();
    expect(Role::Hr->label())->not->toBeEmpty();
    expect(Role::Viewer->label())->not->toBeEmpty();
});
