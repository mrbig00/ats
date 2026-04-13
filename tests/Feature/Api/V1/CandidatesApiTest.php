<?php

use App\Models\Candidate;
use App\Models\PipelineStage;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\PipelineStageSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    $this->seed(PipelineStageSeeder::class);
    $this->user = User::factory()->hr()->create();
});

test('candidates show requires authentication', function (): void {
    $candidate = Candidate::factory()->create();

    $this->getJson("/api/v1/candidates/{$candidate->id}")
        ->assertUnauthorized();
});

test('candidates show returns candidate with profile fields', function (): void {
    Sanctum::actingAs($this->user, ['*']);

    $candidate = Candidate::factory()->create([
        'nationality' => 'HU',
        'german_level' => \App\Enums\GermanLanguageLevel::B1,
        'has_own_car' => true,
        'housing_needed' => null,
    ]);

    $this->getJson("/api/v1/candidates/{$candidate->id}")
        ->assertSuccessful()
        ->assertJsonPath('data.id', $candidate->id)
        ->assertJsonPath('data.nationality', 'HU')
        ->assertJsonPath('data.german_level.value', 'b1')
        ->assertJsonPath('data.has_own_car', true)
        ->assertJsonPath('data.housing_needed', null);
});

test('candidates store creates candidate with optional profile', function (): void {
    Sanctum::actingAs($this->user, ['*']);

    $position = Position::factory()->create();
    $stage = PipelineStage::query()->where('key', 'applied')->first();

    $response = $this->postJson('/api/v1/candidates', [
        'first_name' => 'Api',
        'last_name' => 'Candidate',
        'email' => 'api-candidate@example.com',
        'phone' => null,
        'position_id' => $position->id,
        'pipeline_stage_id' => $stage->id,
        'nationality' => 'DE',
        'driving_license_category' => 'B',
        'has_own_car' => false,
        'german_level' => 'c1',
        'available_from' => '2026-06-15',
        'housing_needed' => true,
    ]);

    $response->assertSuccessful();

    $candidate = Candidate::query()->whereHas('person', fn ($q) => $q->where('email', 'api-candidate@example.com'))->first();
    expect($candidate)->not->toBeNull()
        ->and($candidate->nationality)->toBe('DE')
        ->and($candidate->german_level)->toBe(\App\Enums\GermanLanguageLevel::C1)
        ->and($candidate->has_own_car)->toBeFalse()
        ->and($candidate->housing_needed)->toBeTrue();
});

test('candidates update patches profile fields', function (): void {
    Sanctum::actingAs($this->user, ['*']);

    $candidate = Candidate::factory()->create(['nationality' => 'RO']);

    $this->patchJson("/api/v1/candidates/{$candidate->id}", [
        'nationality' => 'AT',
        'german_level' => 'native',
    ])->assertSuccessful()
        ->assertJsonPath('data.nationality', 'AT')
        ->assertJsonPath('data.german_level.value', 'native');

    expect($candidate->fresh()->nationality)->toBe('AT')
        ->and($candidate->fresh()->german_level)->toBe(\App\Enums\GermanLanguageLevel::Native);
});

test('candidates update is forbidden for viewer role', function (): void {
    $viewer = User::factory()->viewer()->create();
    Sanctum::actingAs($viewer, ['*']);

    $candidate = Candidate::factory()->create();

    $this->patchJson("/api/v1/candidates/{$candidate->id}", [
        'nationality' => 'CH',
    ])->assertForbidden();
});

test('candidates store rejects invalid german_level', function (): void {
    Sanctum::actingAs($this->user, ['*']);

    $position = Position::factory()->create();
    $stage = PipelineStage::query()->first();

    $this->postJson('/api/v1/candidates', [
        'first_name' => 'X',
        'last_name' => 'Y',
        'position_id' => $position->id,
        'pipeline_stage_id' => $stage->id,
        'german_level' => 'not-a-level',
    ])->assertUnprocessable();
});
