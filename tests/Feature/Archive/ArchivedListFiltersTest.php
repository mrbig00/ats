<?php

use App\Livewire\Candidates\CandidateList;
use App\Livewire\Positions\PositionList;
use App\Models\Candidate;
use App\Models\PipelineStage;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\PipelineStageSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(PipelineStageSeeder::class);
});

test('jobs list excludes expired postings by default', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    Position::factory()->create([
        'title' => 'Unique Expired Job Title X7',
        'status' => 'open',
        'closes_at' => \Carbon\CarbonImmutable::today()->subDays(2),
    ]);

    Livewire::test(PositionList::class)
        ->assertDontSee('Unique Expired Job Title X7');
});

test('jobs list shows expired postings when include archived is enabled', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    Position::factory()->create([
        'title' => 'Unique Expired Job Title Y8',
        'status' => 'open',
        'closes_at' => \Carbon\CarbonImmutable::today()->subDays(2),
    ]);

    Livewire::test(PositionList::class)
        ->set('includeArchived', true)
        ->assertSee('Unique Expired Job Title Y8');
});

test('candidates list excludes non hired on expired posting by default', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $position = Position::factory()->create(['status' => 'closed']);
    $stage = PipelineStage::query()->where('key', 'applied')->firstOrFail();
    $candidate = Candidate::factory()->create([
        'position_id' => $position->id,
        'pipeline_stage_id' => $stage->id,
    ]);

    Livewire::test(CandidateList::class)
        ->assertDontSee($candidate->person->fullName());
});

test('candidates list still shows hired candidate on expired posting without include archived', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $position = Position::factory()->create(['status' => 'closed']);
    $hiredStage = PipelineStage::query()->where('key', 'hired')->firstOrFail();
    $candidate = Candidate::factory()->create([
        'position_id' => $position->id,
        'pipeline_stage_id' => $hiredStage->id,
    ]);

    Livewire::test(CandidateList::class)
        ->assertSee($candidate->person->fullName());
});

test('candidates list shows non hired on expired posting when include archived is enabled', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $position = Position::factory()->create(['status' => 'closed']);
    $stage = PipelineStage::query()->where('key', 'applied')->firstOrFail();
    $candidate = Candidate::factory()->create([
        'position_id' => $position->id,
        'pipeline_stage_id' => $stage->id,
    ]);

    Livewire::test(CandidateList::class)
        ->set('includeArchived', true)
        ->assertSee($candidate->person->fullName());
});
