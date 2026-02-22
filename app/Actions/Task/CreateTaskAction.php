<?php

namespace App\Actions\Task;

use App\Data\Task\TaskData;
use App\Events\TaskCreated;
use App\Models\Task;
use App\Repositories\TaskRepository;

class CreateTaskAction
{
    public function __construct(
        private TaskRepository $taskRepository,
    ) {}

    public function handle(TaskData $data): Task
    {
        $task = $this->taskRepository->create($data);

        TaskCreated::dispatch($task->id, $task->user_id);

        return $task;
    }
}
