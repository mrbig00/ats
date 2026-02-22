<?php

namespace App\Data\Task;

use Carbon\CarbonImmutable;

readonly class TaskData
{
    public function __construct(
        public int $userId,
        public string $title,
        public string $priority,
        public CarbonImmutable $dueDate,
        public ?CarbonImmutable $completedAt = null,
    ) {}
}
