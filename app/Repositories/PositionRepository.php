<?php

namespace App\Repositories;

use App\Models\Position;
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

    public function all(): Collection
    {
        return Position::query()->orderBy('title')->get();
    }

    public function find(int $id): ?Position
    {
        return Position::query()->find($id);
    }
}
