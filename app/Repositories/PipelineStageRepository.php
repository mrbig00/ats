<?php

namespace App\Repositories;

use App\Models\PipelineStage;
use Illuminate\Database\Eloquent\Collection;

/**
 * @return Collection<int, PipelineStage>
 */
class PipelineStageRepository
{
    public function allOrdered(): Collection
    {
        return PipelineStage::query()->orderBy('sort_order')->orderBy('id')->get();
    }

    public function find(int $id): ?PipelineStage
    {
        return PipelineStage::query()->find($id);
    }

    public function findByKey(string $key): ?PipelineStage
    {
        return PipelineStage::query()->where('key', $key)->first();
    }
}
