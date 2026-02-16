<?php

use App\Actions\Housing\EndOccupancyAction;
use App\Events\OccupancyEnded;
use App\Models\CalendarEvent;
use App\Models\Occupancy;
use Carbon\CarbonImmutable;
use Database\Factories\EmployeeFactory;
use Database\Factories\OccupancyFactory;
use Database\Factories\RoomFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

test('end occupancy action sets ends_at creates calendar event and dispatches event', function () {
    Event::fake([OccupancyEnded::class]);
    $occupancy = OccupancyFactory::new()->create([
        'starts_at' => '2025-01-01',
        'ends_at' => null,
    ]);

    $action = app(EndOccupancyAction::class);
    $ended = $action->handle($occupancy, CarbonImmutable::parse('2025-06-15'));

    expect($ended->ends_at->toDateString())->toBe('2025-06-15')
        ->and(CalendarEvent::query()->where('type', CalendarEvent::TYPE_ROOM_FREE)->count())->toBe(1);

    Event::assertDispatched(OccupancyEnded::class);
});

test('end occupancy action throws when occupancy already ended', function () {
    $occupancy = OccupancyFactory::new()->ended()->create();

    $action = app(EndOccupancyAction::class);

    $action->handle($occupancy, CarbonImmutable::parse('2025-12-31'));
})->throws(\DomainException::class);
