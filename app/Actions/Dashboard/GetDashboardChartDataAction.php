<?php

namespace App\Actions\Dashboard;

use App\Repositories\ActivityEventRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class GetDashboardChartDataAction
{
    public function __construct(
        private ActivityEventRepository $activityEventRepository,
    ) {}

    /**
     * @return Collection<int, object{date: string, candidate_created: int, candidate_stage_changed: int, employee_hired: int, employee_terminated: int, meeting_scheduled: int, task_created: int}>
     */
    public function handle(CarbonImmutable $from, CarbonImmutable $to): Collection
    {
        return $this->activityEventRepository->getDailyCountsForChart($from, $to);
    }
}
