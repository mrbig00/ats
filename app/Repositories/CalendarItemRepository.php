<?php

namespace App\Repositories;

use App\Models\CalendarItem;
use App\Models\CalendarItemException;
use App\Models\CalendarItemOverride;
use App\Models\CalendarItemRecurrence;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class CalendarItemRepository
{
    /**
     * Base calendar items that may produce occurrences in the given range.
     * Includes one-off items in range and recurring items whose recurrence overlaps the range.
     *
     * @return Collection<int, CalendarItem>
     */
    public function getBaseItemsInRange(CarbonImmutable $start, CarbonImmutable $end, ?array $types): Collection
    {
        $query = CalendarItem::query()
            ->with(['recurrence', 'exceptions', 'overrides', 'calendarItemable'])
            ->where(function ($q) use ($start, $end) {
                $q->whereHas('recurrence', function ($r) use ($start, $end) {
                    $r->where('dtstart', '<', $end)
                        ->where(function ($until) use ($start) {
                            $until->whereNull('until')->orWhere('until', '>=', $start);
                        });
                })->orWhereDoesntHave('recurrence')
                    ->where('start_at', '<', $end)
                    ->where(function ($dates) use ($start) {
                        $dates->whereNull('end_at')->orWhere('end_at', '>=', $start);
                    });
            });

        if ($types !== null && $types !== []) {
            $query->whereIn('type', $types);
        }

        return $query->orderBy('start_at')->get();
    }

    public function createOrUpdateFromCalendarEvent(\App\Models\CalendarEvent $event): CalendarItem
    {
        $type = in_array($event->type, [
            \App\Models\CalendarEvent::TYPE_INTERNAL_MEETING,
            \App\Models\CalendarEvent::TYPE_INTERVIEW,
        ], true) ? CalendarItem::TYPE_MEETING : CalendarItem::TYPE_EVENT;

        $item = CalendarItem::query()
            ->where('calendar_itemable_type', $event->getMorphClass())
            ->where('calendar_itemable_id', $event->id)
            ->first();

        $payload = [
            'type' => $type,
            'title' => $event->title,
            'description' => $event->notes,
            'start_at' => $event->starts_at,
            'end_at' => $event->ends_at,
            'all_day' => $this->isAllDayCalendarEventType($event->type),
            'status' => null,
            'owner_id' => null,
            'color' => null,
            'calendar_itemable_type' => $event->getMorphClass(),
            'calendar_itemable_id' => $event->id,
        ];

        if ($item) {
            $item->update($payload);
            return $item->fresh();
        }

        return CalendarItem::query()->create($payload);
    }

    public function createOrUpdateFromTask(\App\Models\Task $task): CalendarItem
    {
        $item = CalendarItem::query()
            ->where('calendar_itemable_type', $task->getMorphClass())
            ->where('calendar_itemable_id', $task->id)
            ->first();

        $payload = [
            'type' => CalendarItem::TYPE_TASK,
            'title' => $task->title,
            'description' => null,
            'start_at' => $task->due_date->setTime(0, 0),
            'end_at' => $task->due_date->setTime(23, 59, 59),
            'all_day' => true,
            'status' => $task->completed_at ? 'completed' : 'pending',
            'owner_id' => $task->user_id,
            'color' => null,
            'calendar_itemable_type' => $task->getMorphClass(),
            'calendar_itemable_id' => $task->id,
        ];

        if ($item) {
            $item->update($payload);
            return $item->fresh();
        }

        return CalendarItem::query()->create($payload);
    }

    public function deleteByCalendarItemable(Model $model): void
    {
        CalendarItem::query()
            ->where('calendar_itemable_type', $model->getMorphClass())
            ->where('calendar_itemable_id', $model->getKey())
            ->delete();
    }

    private function isAllDayCalendarEventType(string $type): bool
    {
        return in_array($type, [
            \App\Models\CalendarEvent::TYPE_ENTRY_DATE,
            \App\Models\CalendarEvent::TYPE_EXIT_DATE,
            \App\Models\CalendarEvent::TYPE_ROOM_FREE,
        ], true);
    }
}
