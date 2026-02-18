<?php

namespace App\Repositories;

use App\Data\Positions\PositionData;
use App\Data\Positions\PositionFilterData;
use App\Models\Position;
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
        return Position::query()->where('status', 'open')->orderBy('title')->get();
    }

    public function countOpen(): int
    {
        return Position::query()->where('status', 'open')->count();
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

        if ($filters->status !== null && $filters->status !== '') {
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
