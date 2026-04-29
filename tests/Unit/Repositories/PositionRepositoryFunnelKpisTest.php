<?php

use App\Data\Positions\PositionFilterData;
use App\Models\Candidate;
use App\Models\PipelineStage;
use App\Models\Position;
use App\Repositories\PositionRepository;
use Database\Seeders\PipelineStageSeeder;

test('position repository paginates with funnel KPI counts', function () {
    $this->seed(PipelineStageSeeder::class);

    $position = Position::factory()->create();

    $applied = PipelineStage::query()->where('key', 'applied')->firstOrFail();
    $screening = PipelineStage::query()->where('key', 'screening')->firstOrFail();
    $interview = PipelineStage::query()->where('key', 'interview')->firstOrFail();
    $offer = PipelineStage::query()->where('key', 'offer')->firstOrFail();
    $hired = PipelineStage::query()->where('key', 'hired')->firstOrFail();

    Candidate::factory()->count(2)->create(['position_id' => $position->id, 'pipeline_stage_id' => $applied->id]);
    Candidate::factory()->count(3)->create(['position_id' => $position->id, 'pipeline_stage_id' => $screening->id]);
    Candidate::factory()->count(4)->create(['position_id' => $position->id, 'pipeline_stage_id' => $interview->id]);
    Candidate::factory()->count(5)->create(['position_id' => $position->id, 'pipeline_stage_id' => $offer->id]);
    Candidate::factory()->count(6)->create(['position_id' => $position->id, 'pipeline_stage_id' => $hired->id]);

    $filters = new PositionFilterData(
        status: null,
        search: null,
        sortField: 'created_at',
        sortDirection: 'asc',
        perPage: 15,
        includeArchived: true,
    );

    $page = app(PositionRepository::class)->paginate($filters);
    $row = $page->getCollection()->firstWhere('id', $position->id);

    expect($row)->not->toBeNull()
        ->and((int) $row->funnel_applied_count)->toBe(2)
        ->and((int) $row->funnel_interview_count)->toBe(7)
        ->and((int) $row->funnel_offer_count)->toBe(5)
        ->and((int) $row->funnel_hired_count)->toBe(6);
});

