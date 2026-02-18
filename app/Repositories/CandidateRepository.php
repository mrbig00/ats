<?php

namespace App\Repositories;

use App\Data\Candidates\CandidateData;
use App\Data\Candidates\CandidateFilterData;
use App\Models\Candidate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class CandidateRepository
{
    public function create(CandidateData $data): Candidate
    {
        return Candidate::query()->create([
            'person_id' => $data->personId,
            'position_id' => $data->positionId,
            'pipeline_stage_id' => $data->pipelineStageId,
            'source' => $data->source,
            'applied_at' => $data->appliedAt?->toDateTimeString(),
        ]);
    }

    public function find(int $id): ?Candidate
    {
        return Candidate::query()
            ->with(['person', 'position', 'pipelineStage'])
            ->find($id);
    }

    public function updateStage(Candidate $candidate, int $pipelineStageId): Candidate
    {
        $candidate->update(['pipeline_stage_id' => $pipelineStageId]);

        return $candidate->fresh();
    }

    /**
     * Count candidates not in "hired" or "rejected" pipeline stage (active in pipeline).
     */
    public function countActive(): int
    {
        return Candidate::query()
            ->whereHas('pipelineStage', function (Builder $q) {
                $q->whereNotIn('key', ['hired', 'rejected']);
            })
            ->count();
    }

    /**
     * @return LengthAwarePaginator<Candidate>
     */
    public function paginate(CandidateFilterData $filters): LengthAwarePaginator
    {
        $query = Candidate::query()
            ->with(['person', 'position', 'pipelineStage']);

        if ($filters->search !== null && $filters->search !== '') {
            $search = '%'.addcslashes($filters->search, '%_').'%';
            $query->whereHas('person', function (Builder $q) use ($search) {
                $q->where('first_name', 'ilike', $search)
                    ->orWhere('last_name', 'ilike', $search)
                    ->orWhere('email', 'ilike', $search);
            });
        }

        if ($filters->pipelineStageId !== null) {
            $query->where('pipeline_stage_id', $filters->pipelineStageId);
        }

        if ($filters->positionId !== null) {
            $query->where('position_id', $filters->positionId);
        }

        if ($filters->appliedFrom !== null && $filters->appliedFrom !== '') {
            $query->whereDate('applied_at', '>=', $filters->appliedFrom);
        }
        if ($filters->appliedTo !== null && $filters->appliedTo !== '') {
            $query->whereDate('applied_at', '<=', $filters->appliedTo);
        }

        $direction = strtolower($filters->sortDirection) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($this->sortFieldColumn($filters->sortField), $direction);

        return $query->paginate($filters->perPage);
    }

    private function sortFieldColumn(string $field): string
    {
        return match ($field) {
            'applied_at' => 'applied_at',
            'position_id' => 'position_id',
            'pipeline_stage_id' => 'pipeline_stage_id',
            default => 'created_at',
        };
    }
}
