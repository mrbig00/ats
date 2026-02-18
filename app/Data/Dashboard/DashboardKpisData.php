<?php

namespace App\Data\Dashboard;

use Illuminate\Database\Eloquent\Collection;

readonly class DashboardKpisData
{
    public function __construct(
        public int $activeCandidatesCount,
        public int $interviewsThisWeekCount,
        public int $hiredThisMonthCount,
        public int $activeEmployeesCount,
        public int $freeRoomsCount,
        public int $newApplicantsCount,
        public int $upcomingDeparturesCount,
        public int $openPositionsCount,
        /** @var Collection<int, \App\Models\Employee> */
        public Collection $upcomingDeparturesList,
    ) {}
}
