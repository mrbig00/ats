<?php

namespace App\Repositories;

use App\Models\Task;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    /**
     * Upcoming and overdue tasks for the user, sorted by priority (high first) then due_date.
     *
     * @return Collection<int, Task>
     */
    public function getUpcomingAndOverdueForUser(int $userId, int $limit = 15): Collection
    {
        $today = CarbonImmutable::today();
        $cutoff = $today->addDays(30)->toDateString();

        return Task::query()
            ->where('user_id', $userId)
            ->whereNull('completed_at')
            ->where('due_date', '<=', $cutoff)
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 END")
            ->orderBy('due_date')
            ->limit($limit)
            ->get();
    }
}
