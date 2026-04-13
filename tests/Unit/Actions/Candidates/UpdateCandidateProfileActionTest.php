<?php

use App\Actions\Candidates\UpdateCandidateProfileAction;
use App\Data\Candidates\UpdateCandidateProfileData;
use App\Enums\GermanLanguageLevel;
use App\Models\Candidate;
use Database\Seeders\PipelineStageSeeder;

beforeEach(function () {
    $this->seed(PipelineStageSeeder::class);
});

test('update candidate profile action persists allowed fields', function () {
    $candidate = Candidate::factory()->create([
        'nationality' => null,
        'german_level' => null,
    ]);

    $data = new UpdateCandidateProfileData([
        'nationality' => 'DE',
        'german_level' => GermanLanguageLevel::B2->value,
        'has_own_car' => true,
        'housing_needed' => false,
        'driving_license_category' => 'B',
        'available_from' => '2026-05-01',
    ]);

    $updated = app(UpdateCandidateProfileAction::class)->handle($candidate, $data);

    expect($updated->nationality)->toBe('DE')
        ->and($updated->german_level)->toBe(GermanLanguageLevel::B2)
        ->and($updated->has_own_car)->toBeTrue()
        ->and($updated->housing_needed)->toBeFalse()
        ->and($updated->driving_license_category)->toBe('B')
        ->and($updated->available_from?->format('Y-m-d'))->toBe('2026-05-01');
});

test('update candidate profile action is a no-op when patch is empty', function () {
    $candidate = Candidate::factory()->create(['nationality' => 'AT']);

    $updated = app(UpdateCandidateProfileAction::class)->handle($candidate, new UpdateCandidateProfileData([]));

    expect($updated->nationality)->toBe('AT');
});
