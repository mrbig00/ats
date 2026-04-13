<?php

namespace App\Actions\Calendar;

use App\Data\Calendar\CalendarItemFilterData;
use App\Data\Calendar\CalendarItemOccurrenceData;
use App\Models\CalendarItem;
use App\Models\CalendarItemOverride;
use App\Repositories\CalendarItemRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use RRule\RRule;

final class GetCalendarItemsAction
{
    private const MAX_OCCURRENCES = 500;

    public function __construct(
        private CalendarItemRepository $calendarItemRepository,
    ) {}

    /**
     * @return Collection<int, CalendarItemOccurrenceData>
     */
    public function handle(CalendarItemFilterData $filter): Collection
    {
        $items = $this->calendarItemRepository->getBaseItemsInRange(
            $filter->start,
            $filter->end,
            $filter->types
        );

        $occurrences = new Collection;

        foreach ($items as $item) {
            if ($item->recurrence) {
                $occurrences = $occurrences->merge(
                    $this->expandRecurringItem($item, $filter->start, $filter->end)
                );
            } else {
                $occurrences->push($this->singleItemToOccurrence($item));
            }
        }

        return $occurrences->sortBy(fn (CalendarItemOccurrenceData $o) => $o->start->toDateTimeString());
    }

    /**
     * @return Collection<int, CalendarItemOccurrenceData>
     */
    private function expandRecurringItem(CalendarItem $item, CarbonImmutable $rangeStart, CarbonImmutable $rangeEnd): Collection
    {
        $recurrence = $item->recurrence;
        $rruleString = $recurrence->rrule;
        if ($recurrence->until) {
            $rruleString .= ';UNTIL=' . $recurrence->until->setTimezone('UTC')->format('Ymd\THis\Z');
        } elseif ($recurrence->count) {
            $rruleString .= ';COUNT=' . $recurrence->count;
        }

        try {
            $rrule = new RRule('RRULE:' . $rruleString, $recurrence->dtstart);
        } catch (\Throwable) {
            return new Collection;
        }

        $exceptionDates = $item->exceptions->pluck('exception_date')->map(
            fn ($d) => CarbonImmutable::parse($d)->toDateString()
        )->flip();

        $overridesByDate = $item->overrides->keyBy(
            fn (CalendarItemOverride $o) => CarbonImmutable::parse($o->occurrence_date)->toDateString()
        );

        $collected = new Collection;
        $count = 0;

        foreach ($rrule as $occurrence) {
            if ($count >= self::MAX_OCCURRENCES) {
                break;
            }
            $occStart = CarbonImmutable::parse($occurrence->format('Y-m-d H:i:s'), $occurrence->getTimezone());
            if ($occStart >= $rangeEnd) {
                break;
            }
            if ($occStart < $rangeStart) {
                continue;
            }
            $dateKey = $occStart->toDateString();
            if ($exceptionDates->has($dateKey)) {
                continue;
            }

            $override = $overridesByDate->get($dateKey);
            $start = $override?->start_at ? CarbonImmutable::parse($override->start_at) : $occStart;
            $end = $override?->end_at ? CarbonImmutable::parse($override->end_at) : $this->occurrenceEnd($item, $occStart);
            $title = $override?->title ?? $item->title;
            $occurrenceDate = $occStart->toDateString();

            $compositeId = $item->id . ':' . $start->toIso8601String();
            $collected->push(new CalendarItemOccurrenceData(
                id: $compositeId,
                title: $title,
                start: $start,
                end: $end,
                allDay: $item->all_day,
                type: $item->type,
                seriesId: $item->id,
                isRecurring: true,
                occurrenceDate: $occurrenceDate,
                extendedProps: $this->extendedPropsFor($item, $occurrenceDate),
            ));
            $count++;
        }

        return $collected;
    }

    private function singleItemToOccurrence(CalendarItem $item): CalendarItemOccurrenceData
    {
        $start = CarbonImmutable::parse($item->start_at);
        $end = $item->end_at ? CarbonImmutable::parse($item->end_at) : null;

        return new CalendarItemOccurrenceData(
            id: (string) $item->id,
            title: $item->title,
            start: $start,
            end: $end,
            allDay: $item->all_day,
            type: $item->type,
            seriesId: $item->id,
            isRecurring: false,
            occurrenceDate: $start->toDateString(),
            extendedProps: $this->extendedPropsFor($item, $start->toDateString()),
        );
    }

    private function occurrenceEnd(CalendarItem $item, CarbonImmutable $occurrenceStart): ?CarbonImmutable
    {
        if (! $item->end_at) {
            return null;
        }
        $baseStart = CarbonImmutable::parse($item->start_at);
        $baseEnd = CarbonImmutable::parse($item->end_at);
        $durationSeconds = $baseEnd->diffInSeconds($baseStart);
        return $occurrenceStart->addSeconds($durationSeconds);
    }

    private function extendedPropsFor(CalendarItem $item, string $occurrenceDate): array
    {
        $props = [
            'type' => $item->type,
            'seriesId' => $item->id,
            'isRecurring' => $item->recurrence !== null,
            'occurrenceDate' => $occurrenceDate,
        ];

        $source = $item->calendarItemable;
        if ($source instanceof \App\Models\CalendarEvent && $source->room_id) {
            $props['room_id'] = $source->room_id;
        }
        if ($source instanceof \App\Models\CalendarEvent && $source->candidate_id) {
            $props['candidate_id'] = $source->candidate_id;
        }
        if ($source instanceof \App\Models\Task) {
            $props['task_id'] = $source->id;
        }

        return $props;
    }
}
