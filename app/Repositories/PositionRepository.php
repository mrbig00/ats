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

/**
 * @return Collection<int, Position>
 */
class PositionRepository
{
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
        return Position::query()->withCount('candidates')->find($id);
    }

    /**
     * @return LengthAwarePaginator<Position>
     */
    public function paginate(PositionFilterData $filters): LengthAwarePaginator
    {
        $query = Position::query()->withCount('candidates');

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

        $direction = strtolower($filters->sortDirection) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($this->sortFieldColumn($filters->sortField), $direction);

        return $query->paginate($filters->perPage);
    }

    /**
     * @return LengthAwarePaginator<Position>
     */
    public function paginateExpiredSessions(ArchiveListFilterData $filters): LengthAwarePaginator
    {
        $query = Position::query()->withCount('candidates');
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
            'opens_at' => $data->opensAt?->toDateString(),
            'closes_at' => $data->closesAt?->toDateString(),
        ]);

        return $position->fresh();
    }

    public function delete(Position $position): void
    {
        $position->delete();
    }

    private function sortFieldColumn(string $field): string
    {
        return match ($field) {
            'title' => 'title',
            'status' => 'status',
            'opens_at' => 'opens_at',
            'closes_at' => 'closes_at',
            default => 'created_at',
        };
    }
}
