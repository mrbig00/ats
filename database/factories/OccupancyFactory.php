<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Occupancy;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Occupancy>
 */
class OccupancyFactory extends Factory
{
    protected $model = Occupancy::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'employee_id' => Employee::factory(),
            'starts_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'ends_at' => null,
        ];
    }

    /**
     * @return static
     */
    public function ended()
    {
        return $this->state(function (array $attributes) {
            $start = $attributes['starts_at'] instanceof \DateTimeInterface
                ? $attributes['starts_at']
                : new \DateTime($attributes['starts_at']);

            return [
                'ends_at' => fake()->dateTimeBetween($start, 'now'),
            ];
        });
    }
}
