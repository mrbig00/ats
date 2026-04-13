<?php

namespace App\Actions\Task;

use App\Actions\Calendar\SyncCalendarItemAction;
use App\Data\Task\TaskData;
use App\Events\TaskCreated;
use App\Models\Task;
use App\Repositories\TaskRepository;

class CreateTaskAction
{
    public function __construct(
        private TaskRepository $taskRepository,
        private SyncCalendarItemAction $syncCalendarItemAction,
    ) {}

    public function handle(TaskData $data): Task
    {
        $task = $this->taskRepository->create($data);

        $this->syncCalendarItemAction->syncFromModel($task);

        TaskCreated::dispatch($task->id, $task->user_id);

        return $task;
    }
}
