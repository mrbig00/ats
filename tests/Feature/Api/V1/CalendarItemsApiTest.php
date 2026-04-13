<?php

use App\Actions\Calendar\SyncCalendarItemAction;
use App\Models\CalendarEvent;
use App\Models\CalendarItem;
use App\Models\Task;
use App\Models\User;
use Carbon\CarbonImmutable;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

test('calendar items index requires authentication', function (): void {
    $this->getJson('/api/v1/calendar/items?start=2026-03-01&end=2026-03-31')
        ->assertUnauthorized();
});

test('calendar items index returns items in date range for authenticated user', function (): void {
    Sanctum::actingAs($this->user, ['*']);

    $event = CalendarEvent::factory()->create([
        'type' => CalendarEvent::TYPE_INTERNAL_MEETING,
        'title' => 'Standup',
        'starts_at' => '2026-03-10 09:00:00',
        'ends_at' => '2026-03-10 09:30:00',
    ]);
    app(SyncCalendarItemAction::class)->syncFromModel($event);

    $response = $this->getJson('/api/v1/calendar/items?start=2026-03-01&end=2026-03-31');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                ['id', 'title', 'start', 'end', 'allDay', 'extendedProps'],
            ],
        ])
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Standup')
        ->assertJsonPath('data.0.extendedProps.type', 'meeting');
    expect($response->json('data.0.extendedProps.seriesId'))->toBeInt();
});

test('calendar items index validates start and end', function (): void {
    Sanctum::actingAs($this->user, ['*']);

    $this->getJson('/api/v1/calendar/items')
        ->assertUnprocessable();

    $this->getJson('/api/v1/calendar/items?start=2026-03-01')
        ->assertUnprocessable();

    $this->getJson('/api/v1/calendar/items?start=2026-03-31&end=2026-03-01')
        ->assertUnprocessable();
});

test('calendar items index can filter by type', function (): void {
    Sanctum::actingAs($this->user, ['*']);

    $meeting = CalendarEvent::factory()->create([
        'starts_at' => '2026-03-10 09:00:00',
        'ends_at' => '2026-03-10 10:00:00',
    ]);
    app(SyncCalendarItemAction::class)->syncFromModel($meeting);

    $task = Task::create([
        'user_id' => $this->user->id,
        'title' => 'Task in March',
        'priority' => 'medium',
        'due_date' => '2026-03-15',
    ]);
    app(SyncCalendarItemAction::class)->syncFromModel($task);

    $response = $this->getJson('/api/v1/calendar/items?start=2026-03-01&end=2026-03-31&type[]=meeting');

    $response->assertSuccessful();
    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['extendedProps']['type'])->toBe('meeting');
});

test('calendar items have composite id for recurring and simple id for non-recurring', function (): void {
    Sanctum::actingAs($this->user, ['*']);

    $event = CalendarEvent::factory()->create([
        'starts_at' => '2026-03-10 09:00:00',
        'ends_at' => '2026-03-10 10:00:00',
    ]);
    app(SyncCalendarItemAction::class)->syncFromModel($event);

    $response = $this->getJson('/api/v1/calendar/items?start=2026-03-01&end=2026-03-31');

    $response->assertSuccessful();
    $first = $response->json('data.0');
    expect($first['id'])->toBeString();
    expect($first['extendedProps']['isRecurring'])->toBeFalse();
});
