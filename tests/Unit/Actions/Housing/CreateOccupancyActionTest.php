<?php

use App\Actions\Housing\CreateOccupancyAction;
use App\Data\Housing\OccupancyData;
use App\Events\OccupancyCreated;
use App\Models\Occupancy;
use Carbon\CarbonImmutable;
use Database\Factories\ApartmentFactory;
use Database\Factories\EmployeeFactory;
use Database\Factories\RoomFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

test('create occupancy action creates occupancy and dispatches event', function () {
    Event::fake([OccupancyCreated::class]);
    $room = RoomFactory::new()->for(ApartmentFactory::new())->create();
    $employee = EmployeeFactory::new()->create();

    $data = new OccupancyData(
        roomId: $room->id,
        employeeId: $employee->id,
        startsAt: CarbonImmutable::parse('2025-01-01'),
        endsAt: null,
    );

    $action = app(CreateOccupancyAction::class);
    $occupancy = $action->handle($data);

    expect($occupancy)->toBeInstanceOf(Occupancy::class)
        ->and($occupancy->room_id)->toBe($room->id)
        ->and($occupancy->employee_id)->toBe($employee->id)
        ->and($occupancy->starts_at->toDateString())->toBe('2025-01-01')
        ->and($occupancy->ends_at)->toBeNull()
        ->and(Occupancy::count())->toBe(1);

    Event::assertDispatched(OccupancyCreated::class);
});

test('create occupancy action throws when room has overlapping occupancy', function () {
    $room = RoomFactory::new()->for(ApartmentFactory::new())->create();
    $employee = EmployeeFactory::new()->create();
    Occupancy::query()->create([
        'room_id' => $room->id,
        'employee_id' => $employee->id,
        'starts_at' => '2025-01-01',
        'ends_at' => null,
    ]);

    $data = new OccupancyData(
        roomId: $room->id,
        employeeId: EmployeeFactory::new()->create()->id,
        startsAt: CarbonImmutable::parse('2025-06-01'),
        endsAt: null,
    );

    $action = app(CreateOccupancyAction::class);

    $action->handle($data);
})->throws(\DomainException::class);
