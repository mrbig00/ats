<?php

namespace App\Actions\Task;

use App\Models\Task;
use App\Repositories\TaskRepository;

class DeleteTaskAction
{
    public function __construct(
        private TaskRepository $taskRepository,
    ) {}

    public function handle(Task $task): void
    {
        $this->taskRepository->delete($task);
    }
}
