<?php

use App\Actions\Meetings\CreateInternalMeetingAction;
use App\Actions\Meetings\DeleteMeetingAction;
use App\Actions\Meetings\UpdateMeetingAction;
use App\Data\Meetings\MeetingData;
use App\Events\MeetingScheduled;
use App\Models\CalendarEvent;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

test('create internal meeting action creates calendar event and dispatches event', function () {
    Event::fake([MeetingScheduled::class]);

    $data = new MeetingData(
        title: 'Team Sync',
        startsAt: CarbonImmutable::parse('2025-02-20 10:00'),
        endsAt: CarbonImmutable::parse('2025-02-20 11:00'),
        notes: 'Weekly sync',
    );

    $action = app(CreateInternalMeetingAction::class);
    $event = $action->handle($data);

    expect($event)->toBeInstanceOf(CalendarEvent::class)
        ->and($event->type)->toBe(CalendarEvent::TYPE_INTERNAL_MEETING)
        ->and($event->title)->toBe('Team Sync')
        ->and($event->notes)->toBe('Weekly sync')
        ->and($event->candidate_id)->toBeNull()
        ->and(CalendarEvent::count())->toBe(1);

    Event::assertDispatched(MeetingScheduled::class, fn (MeetingScheduled $e) => $e->calendarEventId === $event->id);
});

test('update meeting action updates calendar event', function () {
    $event = CalendarEvent::factory()->create([
        'type' => CalendarEvent::TYPE_INTERNAL_MEETING,
        'title' => 'Old Title',
        'notes' => 'Old notes',
        'starts_at' => '2025-02-20 10:00:00',
        'ends_at' => '2025-02-20 11:00:00',
    ]);

    $data = new MeetingData(
        title: 'Updated Title',
        startsAt: CarbonImmutable::parse('2025-02-21 14:00'),
        endsAt: CarbonImmutable::parse('2025-02-21 15:00'),
        notes: 'Updated notes',
    );

    $action = app(UpdateMeetingAction::class);
    $updated = $action->handle($event, $data);

    expect($updated->title)->toBe('Updated Title')
        ->and($updated->notes)->toBe('Updated notes')
        ->and($updated->starts_at->format('Y-m-d H:i'))->toBe('2025-02-21 14:00')
        ->and($updated->ends_at->format('Y-m-d H:i'))->toBe('2025-02-21 15:00');
});

test('delete meeting action removes calendar event', function () {
    $event = CalendarEvent::factory()->create([
        'type' => CalendarEvent::TYPE_INTERNAL_MEETING,
    ]);

    expect(CalendarEvent::count())->toBe(1);

    $action = app(DeleteMeetingAction::class);
    $action->handle($event);

    expect(CalendarEvent::count())->toBe(0)
        ->and(CalendarEvent::find($event->id))->toBeNull();
});
