<?php

namespace App\Actions\Meetings;

use App\Models\CalendarEvent;
use App\Repositories\CalendarEventRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class ListMeetingsForCalendarAction
{
    public function __construct(
        private CalendarEventRepository $calendarEventRepository,
    ) {}

    /**
     * @return Collection<int, CalendarEvent>
     */
    public function handle(CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        return $this->calendarEventRepository->getEventsForCalendar($start, $end);
    }
}
