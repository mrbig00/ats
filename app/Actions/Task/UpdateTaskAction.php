<?php

namespace App\Actions\Task;

use App\Actions\Calendar\SyncCalendarItemAction;
use App\Data\Task\TaskData;
use App\Models\Task;
use App\Repositories\TaskRepository;

class UpdateTaskAction
{
    public function __construct(
        private TaskRepository $taskRepository,
        private SyncCalendarItemAction $syncCalendarItemAction,
    ) {}

    public function handle(Task $task, TaskData $data): Task
    {
        $task = $this->taskRepository->update($task, $data);

        $this->syncCalendarItemAction->syncFromModel($task);

        return $task;
    }
}
