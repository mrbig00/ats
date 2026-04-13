<?php

namespace Database\Factories;

use App\Enums\GermanLanguageLevel;
use App\Models\Employee;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'status' => Employee::STATUS_ACTIVE,
            'entry_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'exit_date' => null,
            'nationality' => fake()->optional(0.3)->randomElement(['DE', 'HU', 'RO', 'AT']),
            'driving_license_category' => fake()->optional()->randomElement(['B', 'BE', 'C']),
            'has_own_car' => fake()->optional()->boolean(),
            'german_level' => fake()->optional()->randomElement(GermanLanguageLevel::cases())?->value,
            'available_from' => fake()->optional()->date(),
            'housing_needed' => fake()->optional()->boolean(),
        ];
    }
}
