<?php

namespace Database\Factories;

use App\Enums\GermanLanguageLevel;
use App\Models\Candidate;
use App\Models\Person;
use App\Models\PipelineStage;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate>
 */
class CandidateFactory extends Factory
{
    protected $model = Candidate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'position_id' => Position::factory(),
            'pipeline_stage_id' => PipelineStage::query()->inRandomOrder()->first()?->id ?? 1,
            'source' => fake()->optional()->randomElement(['website', 'referral', 'linkedin', 'job_board']),
            'applied_at' => fake()->optional()->dateTimeThisYear(),
            'nationality' => fake()->optional(0.4)->randomElement(['DE', 'HU', 'RO', 'AT', 'PL']),
            'driving_license_category' => fake()->optional()->randomElement(['B', 'BE', 'C', 'C1']),
            'has_own_car' => fake()->optional()->boolean(),
            'german_level' => fake()->optional()->randomElement(GermanLanguageLevel::cases())?->value,
            'available_from' => fake()->optional()->date(),
            'housing_needed' => fake()->optional()->boolean(),
        ];
    }
}
