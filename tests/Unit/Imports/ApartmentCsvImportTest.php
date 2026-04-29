<?php

use App\Imports\ApartmentCsvImport;
use App\Imports\Support\ImportResultCollector;
use App\Models\Apartment;
use App\Models\Room;
use App\Repositories\ApartmentRepository;
use App\Repositories\RoomRepository;
use Illuminate\Support\Collection;

it('creates and updates apartments from csv rows', function () {
    $existing = Apartment::factory()->create([
        'name' => 'Old Name',
        'address' => 'Old Address',
        'notes' => null,
    ]);

    $collector = new ImportResultCollector();
    $import = new ApartmentCsvImport(app(ApartmentRepository::class), app(RoomRepository::class), $collector);

    $import->collection(new Collection([
        ['id' => $existing->id, 'name' => 'New Name', 'address' => '', 'notes' => 'Notes'],
        ['id' => null, 'name' => 'Created Apartment', 'address' => 'Addr', 'notes' => null],
    ]));

    expect(Apartment::findOrFail($existing->id)->name)->toBe('New Name');
    expect($collector->result()->createdCount)->toBe(1);
    expect($collector->result()->updatedCount)->toBe(1);
});

it('imports multiple rooms for one apartment from multiple rows', function () {
    $apartment = Apartment::factory()->create(['name' => 'A1']);

    $collector = new ImportResultCollector();
    $import = new ApartmentCsvImport(app(ApartmentRepository::class), app(RoomRepository::class), $collector);

    $import->collection(new Collection([
        [
            'id' => $apartment->id,
            'name' => 'A1',
            'address' => null,
            'notes' => null,
            'room_id' => null,
            'room_name' => 'Room 1',
            'room_notes' => null,
        ],
        [
            'id' => $apartment->id,
            'name' => 'A1',
            'address' => null,
            'notes' => null,
            'room_id' => null,
            'room_name' => 'Room 2',
            'room_notes' => 'Notes',
        ],
    ]));

    expect(Room::query()->where('apartment_id', $apartment->id)->count())->toBe(2);
});

