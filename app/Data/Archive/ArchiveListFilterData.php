<?php

namespace App\Data\Archive;

readonly class ArchiveListFilterData
{
    public function __construct(
        public ?string $search,
        public string $sortField,
        public string $sortDirection,
        public int $perPage,
        public string $pageName,
    ) {}
}
