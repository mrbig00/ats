<?php

namespace Database\Seeders;

use App\Models\PipelineStage;
use Illuminate\Database\Seeder;

class PipelineStageSeeder extends Seeder
{
    /**
     * @var array<array{key: string, sort_order: int}>
     */
    private array $stages = [
        ['key' => 'applied', 'sort_order' => 10],
        ['key' => 'screening', 'sort_order' => 20],
        ['key' => 'interview', 'sort_order' => 30],
        ['key' => 'offer', 'sort_order' => 40],
        ['key' => 'hired', 'sort_order' => 50],
        ['key' => 'rejected', 'sort_order' => 60],
    ];

    public function run(): void
    {
        foreach ($this->stages as $stage) {
            PipelineStage::query()->firstOrCreate(
                ['key' => $stage['key']],
                ['sort_order' => $stage['sort_order']],
            );
        }
    }
}
