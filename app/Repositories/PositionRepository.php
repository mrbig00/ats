<?php

namespace App\Repositories;

use App\Data\Archive\ArchiveListFilterData;
use App\Data\Positions\PositionData;
use App\Data\Positions\PositionFilterData;
use App\Models\Position;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @return Collection<int, Position>
 */
class PositionRepository
{
    private const FUNNEL_APPLIED = 'applied';
    private const FUNNEL_SCREENING = 'screening';
    private const FUNNEL_INTERVIEW = 'interview';
    private const FUNNEL_OFFER = 'offer';
    private const FUNNEL_HIRED = 'hired';

    public function allOpen(): Collection
    {
        return Position::query()
            ->tap(fn (Builder $q) => Position::applyActiveRecruitmentSessionFilter($q))
            ->orderBy('title')
            ->get();
    }

    /**
     * @return Collection<int, Position>
     */
    public function allActiveRecruitmentSessions(): Collection
    {
        return Position::query()
            ->tap(fn (Builder $q) => Position::applyActiveRecruitmentSessionFilter($q))
            ->orderBy('title')
            ->get();
    }

    public function countOpen(): int
    {
        return Position::query()
            ->tap(fn (Builder $q) => Position::applyActiveRecruitmentSessionFilter($q))
            ->count();
    }

    public function all(): Collection
    {
        return Position::query()->orderBy('title')->get();
    }

    public function find(int $id): ?Position
    {
        $query = Position::query()->withCount('candidates');
        $this->applyFunnelKpisSelect($query);

        return $query->find($id);
    }

    /**
     * @return LengthAwarePaginator<Position>
     */
    public function paginate(PositionFilterData $filters): LengthAwarePaginator
    {
        $query = $this->filteredQuery($filters);

        $direction = strtolower($filters->sortDirection) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($this->sortFieldColumn($filters->sortField), $direction);

        return $query->paginate($filters->perPage);
    }

    /**
     * @return Builder<Position>
     */
    public function exportQuery(PositionFilterData $filters): Builder
    {
        $query = $this->filteredQuery($filters);

        $direction = strtolower($filters->sortDirection) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($this->sortFieldColumn($filters->sortField), $direction);

        return $query;
    }

    /**
     * @return LengthAwarePaginator<Position>
     */
    public function paginateExpiredSessions(ArchiveListFilterData $filters): LengthAwarePaginator
    {
        $query = Position::query()->withCount('candidates');
        $this->applyFunnelKpisSelect($query);
        Position::applyExpiredRecruitmentSessionFilter($query);

        if ($filters->search !== null && $filters->search !== '') {
            $search = '%'.addcslashes($filters->search, '%_').'%';
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'ilike', $search)
                    ->orWhere('description', 'ilike', $search);
            });
        }

        $direction = strtolower($filters->sortDirection) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($this->sortFieldColumn($filters->sortField), $direction);

        return $query->paginate($filters->perPage, ['*'], $filters->pageName);
    }

    public function reopenAfterExpiredSession(Position $position): Position
    {
        $updates = ['status' => 'open'];
        if ($position->closes_at !== null
            && $position->closes_at->toDateString() < CarbonImmutable::today()->toDateString()) {
            $updates['closes_at'] = null;
        }
        $position->update($updates);

        return $position->fresh();
    }

    public function create(PositionData $data): Position
    {
        return Position::query()->create([
            'title' => $data->title,
            'description' => $data->description,
            'status' => $data->status,
            'urgency' => $data->urgency?->value,
            'opens_at' => $data->opensAt?->toDateString(),
            'closes_at' => $data->closesAt?->toDateString(),
        ]);
    }

    public function update(Position $position, PositionData $data): Position
    {
        $position->update([
            'title' => $data->title,
            'description' => $data->description,
            'status' => $data->status,
            'urgency' => $data->urgency?->value,
            'opens_at' => $data->opensAt?->toDateString(),
            'closes_at' => $data->closesAt?->toDateString(),
        ]);

        return $position->fresh();
    }

    public function delete(Position $position): void
    {
        $position->delete();
    }

    /**
     * @param list<int> $ids
     * @return Collection<int, Position>
     */
    public function findManyByIds(array $ids): Collection
    {
        if ($ids === []) {
            return new Collection();
        }

        return Position::query()->whereIn('id', $ids)->get();
    }

    /**
     * @param array{
     *   title:string,
     *   description:?string,
     *   status:string,
     *   urgency:?string,
     *   opens_at:?string,
     *   closes_at:?string
     * } $attributes
     */
    public function createFromCsv(array $attributes): Position
    {
        return Position::query()->create($attributes);
    }

    /**
     * @param array{
     *   title:string,
     *   description:?string,
     *   status:string,
     *   urgency:?string,
     *   opens_at:?string,
     *   closes_at:?string
     * } $attributes
     */
    public function updateFromCsv(Position $position, array $attributes): Position
    {
        $position->update($attributes);

        return $position->fresh() ?? $position;
    }

    private function sortFieldColumn(string $field): string
    {
        return match ($field) {
            'title' => 'positions.title',
            'status' => 'positions.status',
            'opens_at' => 'positions.opens_at',
            'closes_at' => 'positions.closes_at',
            default => 'positions.created_at',
        };
    }

    private function applyFunnelKpisSelect(Builder $query): void
    {
        $subQuery = DB::table('candidates')
            ->join('pipeline_stages', 'pipeline_stages.id', '=', 'candidates.pipeline_stage_id')
            ->select('candidates.position_id')
            ->selectRaw(
                "SUM(CASE WHEN pipeline_stages.key = ? THEN 1 ELSE 0 END) AS funnel_applied_count",
                [self::FUNNEL_APPLIED],
            )
            ->selectRaw(
                "SUM(CASE WHEN pipeline_stages.key IN (?, ?) THEN 1 ELSE 0 END) AS funnel_interview_count",
                [self::FUNNEL_SCREENING, self::FUNNEL_INTERVIEW],
            )
            ->selectRaw(
                "SUM(CASE WHEN pipeline_stages.key = ? THEN 1 ELSE 0 END) AS funnel_offer_count",
                [self::FUNNEL_OFFER],
            )
            ->selectRaw(
                "SUM(CASE WHEN pipeline_stages.key = ? THEN 1 ELSE 0 END) AS funnel_hired_count",
                [self::FUNNEL_HIRED],
            )
            ->groupBy('candidates.position_id');

        $query->leftJoinSub($subQuery, 'funnel', function ($join) {
            $join->on('positions.id', '=', 'funnel.position_id');
        });

        $query->addSelect([
            'positions.*',
            DB::raw('COALESCE(funnel.funnel_applied_count, 0) AS funnel_applied_count'),
            DB::raw('COALESCE(funnel.funnel_interview_count, 0) AS funnel_interview_count'),
            DB::raw('COALESCE(funnel.funnel_offer_count, 0) AS funnel_offer_count'),
            DB::raw('COALESCE(funnel.funnel_hired_count, 0) AS funnel_hired_count'),
        ]);
    }

    /**
     * @return Builder<Position>
     */
    private function filteredQuery(PositionFilterData $filters): Builder
    {
        $query = Position::query()->withCount('candidates');
        $this->applyFunnelKpisSelect($query);

        if (! $filters->includeArchived) {
            Position::applyActiveRecruitmentSessionFilter($query);
        } elseif ($filters->status !== null && $filters->status !== '') {
            $query->where('status', $filters->status);
        }

        if ($filters->search !== null && $filters->search !== '') {
            $search = '%'.addcslashes($filters->search, '%_').'%';
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'ilike', $search)
                    ->orWhere('description', 'ilike', $search);
            });
        }

        return $query;
    }
}
