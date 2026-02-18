<?php

namespace App\Actions\Dashboard;

use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\Collection;

class GetDashboardTasksAction
{
    public function __construct(
        private TaskRepository $taskRepository,
    ) {}

    /**
     * @return Collection<int, \App\Models\Task>
     */
    public function handle(int $userId, int $limit = 15): Collection
    {
        return $this->taskRepository->getUpcomingAndOverdueForUser($userId, $limit);
    }
}
