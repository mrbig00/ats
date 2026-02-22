<?php

namespace App\Data\Task;

use Carbon\CarbonImmutable;

readonly class TaskFilterData
{
    public function __construct(
        public int $userId,
        public ?string $search = null,
        public ?string $priority = null,
        public ?CarbonImmutable $dueDateFrom = null,
        public ?CarbonImmutable $dueDateTo = null,
        public ?bool $completed = null,
        public string $sortField = 'due_date',
        public string $sortDirection = 'asc',
        public int $perPage = 15,
    ) {}
}
