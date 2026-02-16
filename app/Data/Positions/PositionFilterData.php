<?php

namespace App\Data\Positions;

readonly class PositionFilterData
{
    public function __construct(
        public ?string $status,
        public ?string $search,
        public string $sortField,
        public string $sortDirection,
        public int $perPage,
    ) {}
}
