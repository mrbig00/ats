<?php

use App\Actions\Candidates\UpdateCandidateStageAction;
use App\Data\Candidates\UpdateCandidateStageData;
use App\Models\Candidate;
use App\Models\PipelineStage;
use App\Models\Position;
use App\Models\User;
use App\Support\CandidatePipelineStageActivity;
use Database\Seeders\PipelineStageSeeder;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->seed(PipelineStageSeeder::class);
});

test('pipeline stage changes are logged with authenticated causer', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $stages = PipelineStage::query()->orderBy('id')->take(2)->get();
    expect($stages)->toHaveCount(2);

    $candidate = Candidate::factory()->create([
        'position_id' => Position::factory()->create()->id,
        'pipeline_stage_id' => $stages[0]->id,
    ]);

    app(UpdateCandidateStageAction::class)->handle(new UpdateCandidateStageData(
        candidateId: $candidate->id,
        pipelineStageId: $stages[1]->id,
    ));

    $last = Activity::query()->forSubject($candidate)->latest()->first();
    expect($last)->not->toBeNull()
        ->and($last->causer_id)->toBe($user->id)
        ->and($last->description)->toBe('candidate.activity.updated');

    $fresh = $candidate->fresh();
    $fresh->load(['activities' => fn ($q) => $q->with('causer')->latest()]);
    $setter = CandidatePipelineStageActivity::currentStageSetter($fresh);
    expect($setter)->not->toBeNull()
        ->and($setter['id'])->toBe($user->id)
        ->and($setter['name'])->toBe($user->name);
});
