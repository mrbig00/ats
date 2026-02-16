<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    protected $model = Contract::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('-2 years', 'now');

        return [
            'employee_id' => Employee::factory(),
            'type' => fake()->randomElement(['Permanent', 'Fixed-term', 'Trial']),
            'starts_at' => $startsAt,
            'ends_at' => fake()->boolean(30) ? fake()->dateTimeBetween($startsAt, '+2 years') : null,
            'notes' => fake()->boolean(20) ? fake()->sentence() : null,
        ];
    }
}
