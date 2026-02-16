<?php

namespace Database\Factories;

use App\Models\CalendarEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CalendarEvent>
 */
class CalendarEventFactory extends Factory
{
    protected $model = CalendarEvent::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('+1 day', '+1 month');

        return [
            'type' => CalendarEvent::TYPE_INTERNAL_MEETING,
            'title' => fake()->sentence(3),
            'notes' => fake()->optional()->paragraph(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify('+1 hour'),
            'candidate_id' => null,
            'room_id' => null,
        ];
    }

    public function interview(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CalendarEvent::TYPE_INTERVIEW,
        ]);
    }
}
