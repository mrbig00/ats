<?php

namespace App\Repositories;

use App\Data\Archive\ArchiveListFilterData;
use App\Data\Candidates\CandidateData;
use App\Data\Candidates\CandidateFilterData;
use App\Data\Candidates\UpdateCandidateProfileData;
use App\Models\Candidate;
use App\Models\Position;
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
            'nationality' => $data->nationality,
            'driving_license_category' => $data->drivingLicenseCategory,
            'has_own_car' => $data->hasOwnCar,
            'german_level' => $data->germanLevel?->value,
            'available_from' => $data->availableFrom?->toDateString(),
            'housing_needed' => $data->housingNeeded,
        ]);
    }

    public function applyProfilePatch(Candidate $candidate, UpdateCandidateProfileData $data): Candidate
    {
        $candidate->update($data->attributes);

        return $candidate->fresh() ?? $candidate;
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

        if (! $filters->includeArchived) {
            $query->where(function (Builder $q) {
                $q->whereHas('pipelineStage', function (Builder $sq) {
                    $sq->where('key', 'hired');
                })->orWhereIn(
                    'position_id',
                    Position::query()
                        ->tap(fn (Builder $sub) => Position::applyActiveRecruitmentSessionFilter($sub))
                        ->select('positions.id'),
                );
            });
        }

        $direction = strtolower($filters->sortDirection) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($this->sortFieldColumn($filters->sortField), $direction);

        return $query->paginate($filters->perPage);
    }

    /**
     * Candidates on expired job postings who are not hired (stale pipeline).
     *
     * @return LengthAwarePaginator<Candidate>
     */
    public function paginateArchivedPipelineApplications(ArchiveListFilterData $filters): LengthAwarePaginator
    {
        $query = Candidate::query()
            ->with(['person', 'position', 'pipelineStage'])
            ->whereIn(
                'position_id',
                Position::query()
                    ->tap(fn (Builder $sub) => Position::applyExpiredRecruitmentSessionFilter($sub))
                    ->select('positions.id'),
            )
            ->whereHas('pipelineStage', function (Builder $sq) {
                $sq->where('key', '!=', 'hired');
            });

        if ($filters->search !== null && $filters->search !== '') {
            $search = '%'.addcslashes($filters->search, '%_').'%';
            $query->whereHas('person', function (Builder $q) use ($search) {
                $q->where('first_name', 'ilike', $search)
                    ->orWhere('last_name', 'ilike', $search)
                    ->orWhere('email', 'ilike', $search);
            });
        }

        $direction = strtolower($filters->sortDirection) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($this->sortFieldColumn($filters->sortField), $direction);

        return $query->paginate($filters->perPage, ['*'], $filters->pageName);
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
