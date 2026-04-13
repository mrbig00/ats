<?php

namespace App\Actions\Task;

use App\Actions\Calendar\SyncCalendarItemAction;
use App\Models\Task;
use App\Repositories\TaskRepository;

class DeleteTaskAction
{
    public function __construct(
        private TaskRepository $taskRepository,
        private SyncCalendarItemAction $syncCalendarItemAction,
    ) {}

    public function handle(Task $task): void
    {
        $this->syncCalendarItemAction->deleteForModel($task);

        $this->taskRepository->delete($task);
    }
}
