<?php

use App\Models\CalendarEvent;
use App\Models\CalendarItem;
use App\Models\Task;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $meetingTypes = [
            CalendarEvent::TYPE_INTERNAL_MEETING,
            CalendarEvent::TYPE_INTERVIEW,
        ];
        $allDayTypes = [
            CalendarEvent::TYPE_ENTRY_DATE,
            CalendarEvent::TYPE_EXIT_DATE,
            CalendarEvent::TYPE_ROOM_FREE,
        ];

        foreach (CalendarEvent::query()->get() as $event) {
            $type = in_array($event->type, $meetingTypes, true)
                ? CalendarItem::TYPE_MEETING
                : CalendarItem::TYPE_EVENT;
            $allDay = in_array($event->type, $allDayTypes, true);

            CalendarItem::query()->create([
                'type' => $type,
                'title' => $event->title,
                'description' => $event->notes,
                'start_at' => $event->starts_at,
                'end_at' => $event->ends_at,
                'all_day' => $allDay,
                'status' => null,
                'owner_id' => null,
                'color' => null,
                'calendar_itemable_type' => $event->getMorphClass(),
                'calendar_itemable_id' => $event->id,
            ]);
        }

        foreach (Task::query()->get() as $task) {
            $dateStr = $task->due_date->toDateString();
            $startAt = $dateStr . ' 00:00:00';
            $endAt = $dateStr . ' 23:59:59';

            CalendarItem::query()->create([
                'type' => CalendarItem::TYPE_TASK,
                'title' => $task->title,
                'description' => null,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'all_day' => true,
                'status' => $task->completed_at ? 'completed' : 'pending',
                'owner_id' => $task->user_id,
                'color' => null,
                'calendar_itemable_type' => $task->getMorphClass(),
                'calendar_itemable_id' => $task->id,
            ]);
        }
    }

    public function down(): void
    {
        CalendarItem::query()
            ->whereIn('calendar_itemable_type', [CalendarEvent::class, Task::class])
            ->forceDelete();
    }
};
