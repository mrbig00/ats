<?php

namespace App\Actions\Dashboard;

use App\Repositories\CalendarEventRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class GetDashboardCalendarEventsAction
{
    public function __construct(
        private CalendarEventRepository $calendarEventRepository,
    ) {}

    /**
     * @return Collection<int, \App\Models\CalendarEvent>
     */
    public function handle(CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        return $this->calendarEventRepository->getEventsForDashboardCalendar($start, $end);
    }
}
