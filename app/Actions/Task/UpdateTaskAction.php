<?php

namespace App\Actions\Task;

use App\Data\Task\TaskData;
use App\Models\Task;
use App\Repositories\TaskRepository;

class UpdateTaskAction
{
    public function __construct(
        private TaskRepository $taskRepository,
    ) {}

    public function handle(Task $task, TaskData $data): Task
    {
        return $this->taskRepository->update($task, $data);
    }
}
