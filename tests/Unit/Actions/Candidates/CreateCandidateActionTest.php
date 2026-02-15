<?php

use App\Actions\Candidates\CreateCandidateAction;
use App\Data\Candidates\CandidateData;
use App\Data\Candidates\PersonData;
use App\Events\CandidateCreated;
use App\Models\Candidate;
use App\Models\PipelineStage;
use App\Models\Position;
use Carbon\CarbonImmutable;
use Database\Seeders\PipelineStageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(PipelineStageSeeder::class);
});

test('create candidate action creates person and candidate and dispatches event', function () {
    Event::fake([CandidateCreated::class]);
    $position = Position::factory()->create();
    $stage = PipelineStage::query()->first();

    $personData = new PersonData(
        firstName: 'Jane',
        lastName: 'Doe',
        email: 'jane@example.com',
        phone: null,
    );
    $candidateData = new CandidateData(
        personId: 0,
        positionId: $position->id,
        pipelineStageId: $stage->id,
        source: 'website',
        appliedAt: CarbonImmutable::parse('2025-01-15'),
    );

    $action = app(CreateCandidateAction::class);
    $candidate = $action->handle($personData, $candidateData);

    expect($candidate)->toBeInstanceOf(Candidate::class)
        ->and($candidate->person->first_name)->toBe('Jane')
        ->and($candidate->person->last_name)->toBe('Doe')
        ->and($candidate->position_id)->toBe($position->id)
        ->and($candidate->pipeline_stage_id)->toBe($stage->id)
        ->and(Candidate::count())->toBe(1);

    Event::assertDispatched(CandidateCreated::class);
});
