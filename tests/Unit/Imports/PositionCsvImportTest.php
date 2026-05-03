<?php

use App\Imports\PositionCsvImport;
use App\Imports\Support\ImportResultCollector;
use App\Models\Position;
use App\Repositories\PositionRepository;
use Illuminate\Support\Collection;

it('creates and updates positions from csv rows', function () {
    $existing = Position::factory()->create([
        'title' => 'Old Title',
        'status' => 'open',
        'description' => null,
    ]);

    $collector = new ImportResultCollector();
    $import = new PositionCsvImport(app(PositionRepository::class), $collector);

    $import->collection(new Collection([
        [
            'id' => $existing->id,
            'title' => 'New Title',
            'description' => 'Desc',
            'status' => 'closed',
            'urgency' => null,
            'opens_at' => null,
            'closes_at' => null,
        ],
        [
            'id' => null,
            'title' => 'Created Job',
            'description' => null,
            'status' => 'open',
            'urgency' => null,
            'opens_at' => null,
            'closes_at' => null,
        ],
    ]));

    expect(Position::findOrFail($existing->id)->title)->toBe('New Title');
    expect($collector->result()->createdCount)->toBe(1);
    expect($collector->result()->updatedCount)->toBe(1);
});

it('maps urgency synonym high to urgent when importing', function () {
    $collector = new ImportResultCollector();
    $import = new PositionCsvImport(app(PositionRepository::class), $collector);

    $import->collection(new Collection([
        [
            'id' => null,
            'title' => 'Urgent role',
            'description' => null,
            'status' => 'open',
            'urgency' => 'high',
            'opens_at' => null,
            'closes_at' => null,
        ],
    ]));

    $position = Position::query()->where('title', 'Urgent role')->firstOrFail();
    expect($position->urgency)->not->toBeNull();
    expect($position->urgency->value)->toBe('urgent');
});

