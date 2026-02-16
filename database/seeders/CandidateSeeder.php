<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Person;
use App\Models\PipelineStage;
use App\Models\Position;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    /**
     * Seed candidates with persons, using existing positions and pipeline stages.
     */
    public function run(): void
    {
        $stages = PipelineStage::query()->orderBy('sort_order')->get();
        if ($stages->isEmpty()) {
            $this->command->warn('No pipeline stages found. Run PipelineStageSeeder first.');

            return;
        }

        $positions = Position::query()->get();
        if ($positions->isEmpty()) {
            $positions = $this->seedPositions();
        }

        $sources = ['website', 'referral', 'linkedin', 'job_board', 'agency', null];

        foreach (range(1, 40) as $i) {
            $person = Person::factory()->create();
            $stage = $stages->random();
            $position = $positions->random();

            Candidate::create([
                'person_id' => $person->id,
                'position_id' => $position->id,
                'pipeline_stage_id' => $stage->id,
                'source' => $sources[array_rand($sources)],
                'applied_at' => fake()->optional(0.9)->dateTimeBetween('-6 months', 'now'),
            ]);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Position>
     */
    private function seedPositions(): \Illuminate\Database\Eloquent\Collection
    {
        $titles = [
            'Senior PHP Developer',
            'Frontend Engineer',
            'DevOps Engineer',
            'Product Manager',
            'UX Designer',
        ];

        foreach ($titles as $title) {
            Position::firstOrCreate(
                ['title' => $title],
                [
                    'description' => fake()->optional()->paragraph(),
                    'status' => 'open',
                    'opens_at' => now()->subMonths(2),
                    'closes_at' => null,
                ]
            );
        }

        return Position::query()->get();
    }
}
