<?php

namespace Database\Factories;

use App\Models\Apartment;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'apartment_id' => Apartment::factory(),
            'name' => 'Room '.fake()->numberBetween(1, 99),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
