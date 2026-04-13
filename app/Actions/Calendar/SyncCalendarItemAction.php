<?php

namespace App\Actions\Calendar;

use App\Models\CalendarEvent;
use App\Models\CalendarItem;
use App\Models\Task;
use App\Repositories\CalendarItemRepository;
use Illuminate\Database\Eloquent\Model;

class SyncCalendarItemAction
{
    public function __construct(
        private CalendarItemRepository $calendarItemRepository,
    ) {}

    public function syncFromModel(CalendarEvent|Task $model): CalendarItem
    {
        return $model instanceof CalendarEvent
            ? $this->calendarItemRepository->createOrUpdateFromCalendarEvent($model)
            : $this->calendarItemRepository->createOrUpdateFromTask($model);
    }

    public function deleteForModel(Model $model): void
    {
        $this->calendarItemRepository->deleteByCalendarItemable($model);
    }
}
