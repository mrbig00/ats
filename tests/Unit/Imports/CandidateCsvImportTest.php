<?php

use App\Imports\CandidateCsvImport;
use App\Imports\Support\ImportResultCollector;
use App\Models\Candidate;
use App\Models\Person;
use App\Models\PipelineStage;
use App\Models\Position;
use App\Repositories\CandidateRepository;
use App\Repositories\PersonRepository;
use Illuminate\Support\Collection;

it('creates and updates candidates from csv rows', function () {
    $person = Person::factory()->create();
    $position = Position::factory()->create();
    $stage = PipelineStage::query()->first();

    if ($stage === null) {
        $stage = PipelineStage::query()->create(['key' => 'applied', 'sort_order' => 1]);
    }

    $existing = Candidate::factory()->create([
        'person_id' => $person->id,
        'position_id' => $position->id,
        'pipeline_stage_id' => $stage->id,
        'source' => null,
    ]);

    $collector = new ImportResultCollector();
    $import = new CandidateCsvImport(
        app(CandidateRepository::class),
        app(PersonRepository::class),
        $collector,
    );

    $import->collection(new Collection([
        [
            'id' => $existing->id,
            'person_id' => $person->id,
            'person_first_name' => null,
            'person_last_name' => null,
            'person_email' => null,
            'person_phone' => null,
            'position_id' => $position->id,
            'pipeline_stage_id' => $stage->id,
            'source' => 'referral',
            'applied_at' => null,
            'nationality' => null,
            'driving_license_category' => null,
            'has_own_car' => null,
            'german_level' => null,
            'available_from' => null,
            'housing_needed' => null,
        ],
        [
            'id' => null,
            'person_id' => $person->id,
            'person_first_name' => null,
            'person_last_name' => null,
            'person_email' => null,
            'person_phone' => null,
            'position_id' => $position->id,
            'pipeline_stage_id' => $stage->id,
            'source' => null,
            'applied_at' => null,
            'nationality' => null,
            'driving_license_category' => null,
            'has_own_car' => null,
            'german_level' => null,
            'available_from' => null,
            'housing_needed' => null,
        ],
    ]));

    expect(Candidate::findOrFail($existing->id)->source)->toBe('referral');
    expect($collector->result()->createdCount)->toBe(1);
    expect($collector->result()->updatedCount)->toBe(1);
});

it('creates a person from csv and links candidate when person_id is missing', function () {
    $position = Position::factory()->create();
    $stage = PipelineStage::query()->first();

    if ($stage === null) {
        $stage = PipelineStage::query()->create(['key' => 'applied', 'sort_order' => 1]);
    }

    $collector = new ImportResultCollector();
    $import = new CandidateCsvImport(
        app(CandidateRepository::class),
        app(PersonRepository::class),
        $collector,
    );

    $import->collection(new Collection([
        [
            'id' => null,
            'person_id' => null,
            'person_first_name' => 'Jane',
            'person_last_name' => 'Doe',
            'person_email' => 'jane.doe@example.com',
            'person_phone' => '+49123456789',
            'position_id' => $position->id,
            'pipeline_stage_id' => $stage->id,
            'source' => null,
            'applied_at' => null,
            'nationality' => null,
            'driving_license_category' => null,
            'has_own_car' => null,
            'german_level' => null,
            'available_from' => null,
            'housing_needed' => null,
        ],
    ]));

    $person = Person::query()->where('email', 'jane.doe@example.com')->first();
    expect($person)->not->toBeNull();
    expect($person?->first_name)->toBe('Jane');
    expect($person?->last_name)->toBe('Doe');
    expect($person?->phone)->toBe('+49123456789');

    expect(Candidate::query()->where('person_id', $person?->id)->exists())->toBeTrue();
    expect($collector->result()->createdCount)->toBe(1);
    expect($collector->result()->failedCount)->toBe(0);
});

