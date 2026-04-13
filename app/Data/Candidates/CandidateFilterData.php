<?php

namespace App\Data\Candidates;

readonly class CandidateFilterData
{
    public ?string $search;

    public ?int $pipelineStageId;

    public ?int $positionId;

    public ?string $appliedFrom;

    public ?string $appliedTo;

    public string $sortField;

    public string $sortDirection;

    public int $perPage;

    public bool $includeArchived;

    public function __construct(
        ?string $search,
        ?int $pipelineStageId,
        ?int $positionId,
        ?string $appliedFrom,
        ?string $appliedTo,
        string $sortField,
        string $sortDirection,
        int $perPage,
        bool $includeArchived = false,
    ) {
        $this->search = $search;
        $this->pipelineStageId = $pipelineStageId;
        $this->positionId = $positionId;
        $this->appliedFrom = $appliedFrom;
        $this->appliedTo = $appliedTo;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        $this->perPage = $perPage;
        $this->includeArchived = $includeArchived;
    }
}
