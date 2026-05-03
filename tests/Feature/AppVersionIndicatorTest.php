<?php

use App\Models\User;

test('authenticated layout shows version when app.version is configured', function () {
    config(['app.version' => '9.9.9-feature-test']);

    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('9.9.9-feature-test', false);
});

test('login page shows version when app.version is configured', function () {
    config(['app.version' => '9.9.9-auth-layout']);

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('9.9.9-auth-layout', false);
});
