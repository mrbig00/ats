<?php

use App\Actions\Calendar\GetCalendarItemsAction;
use App\Data\Calendar\CalendarItemFilterData;
use App\Models\CalendarItem;
use App\Models\CalendarItemRecurrence;
use App\Models\CalendarEvent;
use Carbon\CarbonImmutable;

test('returns single occurrence for non-recurring calendar item', function (): void {
    $event = CalendarEvent::factory()->create([
        'starts_at' => '2026-03-10 09:00:00',
        'ends_at' => '2026-03-10 10:00:00',
    ]);
    $item = CalendarItem::query()->create([
        'type' => CalendarItem::TYPE_MEETING,
        'title' => 'One-off',
        'start_at' => '2026-03-10 09:00:00',
        'end_at' => '2026-03-10 10:00:00',
        'all_day' => false,
        'calendar_itemable_type' => $event->getMorphClass(),
        'calendar_itemable_id' => $event->id,
    ]);

    $filter = new CalendarItemFilterData(
        CarbonImmutable::parse('2026-03-01'),
        CarbonImmutable::parse('2026-03-31'),
        null,
    );
    $action = app(GetCalendarItemsAction::class);
    $occurrences = $action->handle($filter);

    expect($occurrences)->toHaveCount(1);
    expect($occurrences->first()->title)->toBe('One-off');
    expect($occurrences->first()->isRecurring)->toBeFalse();
});

test('expands recurring item into multiple occurrences in range', function (): void {
    $event = CalendarEvent::factory()->create([
        'starts_at' => '2026-03-03 09:00:00',
        'ends_at' => '2026-03-03 09:30:00',
    ]);
    $item = CalendarItem::query()->create([
        'type' => CalendarItem::TYPE_MEETING,
        'title' => 'Weekly Standup',
        'start_at' => '2026-03-03 09:00:00',
        'end_at' => '2026-03-03 09:30:00',
        'all_day' => false,
        'calendar_itemable_type' => $event->getMorphClass(),
        'calendar_itemable_id' => $event->id,
    ]);
    CalendarItemRecurrence::query()->create([
        'calendar_item_id' => $item->id,
        'rrule' => 'FREQ=WEEKLY;BYDAY=MO',
        'timezone' => 'UTC',
        'dtstart' => '2026-03-03 09:00:00',
        'until' => '2026-03-24 23:59:59',
        'count' => null,
    ]);

    $filter = new CalendarItemFilterData(
        CarbonImmutable::parse('2026-03-01'),
        CarbonImmutable::parse('2026-03-31'),
        null,
    );
    $action = app(GetCalendarItemsAction::class);
    $occurrences = $action->handle($filter);

    expect($occurrences->count())->toBeGreaterThanOrEqual(3);
    $titles = $occurrences->pluck('title')->unique();
    expect($titles->toArray())->toBe(['Weekly Standup']);
    expect($occurrences->first()->isRecurring)->toBeTrue();
});
