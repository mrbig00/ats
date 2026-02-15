<?php

use App\Models\Candidate;
use App\Models\User;
use Database\Seeders\PipelineStageSeeder;

beforeEach(function () {
    $this->seed(PipelineStageSeeder::class);
});

test('guests cannot view candidates list', function () {
    $response = $this->get(route('candidates.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can view candidates list', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('candidates.index'));
    $response->assertOk();
});

test('candidates list shows candidates when present', function () {
    $user = User::factory()->create();
    $candidate = Candidate::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('candidates.index'));
    $response->assertOk();
    $response->assertSee($candidate->person->fullName());
});
