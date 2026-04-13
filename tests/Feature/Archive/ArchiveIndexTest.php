<?php

use App\Models\User;

test('guests cannot view archive', function () {
    $this->get(route('archive.index'))->assertRedirect(route('login'));
});

test('authenticated users can view archive', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('archive.index'))->assertOk();
});
