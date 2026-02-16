<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Occupancy;
use App\Models\Person;
use App\Models\Room;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Seed employees with all dependencies: persons, housing (apartments, rooms, occupancies), contracts.
     */
    public function run(): void
    {
        $persons = $this->seedPersons();
        $employees = $this->seedEmployees($persons);
        $rooms = $this->seedHousing();
        $this->seedOccupancies($employees, $rooms);
        $this->seedContracts($employees);
    }

    /**
     * Create persons that do not yet have an employee record (e.g. not used by CandidateSeeder as hired).
     *
     * @return \Illuminate\Support\Collection<int, Person>
     */
    private function seedPersons(): \Illuminate\Support\Collection
    {
        $count = 20;
        $persons = collect();

        for ($i = 0; $i < $count; $i++) {
            $persons->push(Person::factory()->create());
        }

        return $persons;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Person>  $persons
     * @return \Illuminate\Support\Collection<int, Employee>
     */
    private function seedEmployees(\Illuminate\Support\Collection $persons): \Illuminate\Support\Collection
    {
        $employees = collect();
        $now = CarbonImmutable::now();
        $statuses = [
            ['status' => Employee::STATUS_ACTIVE, 'weight' => 14],
            ['status' => Employee::STATUS_LEAVING, 'weight' => 2],
            ['status' => Employee::STATUS_LEFT, 'weight' => 4],
        ];

        foreach ($persons as $index => $person) {
            if (Employee::query()->where('person_id', $person->id)->exists()) {
                continue;
            }

            $entryDate = $now->subDays(fake()->numberBetween(30, 800))->startOfDay();
            $statusConfig = $this->weightedRandom($statuses);
            $status = $statusConfig['status'];
            $exitDate = null;

            if ($status === Employee::STATUS_LEAVING) {
                $exitDate = $now->addDays(fake()->numberBetween(7, 60));
            } elseif ($status === Employee::STATUS_LEFT) {
                $exitDate = $entryDate->addDays(fake()->numberBetween(90, 600));
            }

            $employees->push(Employee::create([
                'person_id' => $person->id,
                'status' => $status,
                'entry_date' => $entryDate->toDateString(),
                'exit_date' => $exitDate?->toDateString(),
            ]));
        }

        return $employees;
    }

    /**
     * @return \Illuminate\Support\Collection<int, Room>
     */
    private function seedHousing(): \Illuminate\Support\Collection
    {
        $apartments = collect([
            Apartment::firstOrCreate(
                ['name' => 'Main Street 10'],
                ['address' => 'Main Street 10, 10115 Berlin', 'notes' => null]
            ),
            Apartment::firstOrCreate(
                ['name' => 'Park Residence A'],
                ['address' => 'Parkweg 5, 10115 Berlin', 'notes' => null]
            ),
            Apartment::firstOrCreate(
                ['name' => 'Central Building B'],
                ['address' => 'Central Square 2, 10115 Berlin', 'notes' => null]
            ),
        ]);

        $rooms = collect();
        $roomNames = ['101', '102', '201', '202', '301', '302', 'A1', 'A2', 'B1'];
        $used = 0;

        foreach ($apartments as $apartment) {
            $numRooms = 2 + ($used % 3);
            for ($i = 0; $i < $numRooms; $i++) {
                $name = $roomNames[$used % count($roomNames)].'-'.$apartment->id;
                $rooms->push(Room::firstOrCreate(
                    ['apartment_id' => $apartment->id, 'name' => $name],
                    ['notes' => null]
                ));
                $used++;
            }
        }

        return $rooms;
    }

    /**
     * Assign some active employees to rooms (one occupancy per room, no overlap).
     *
     * @param  \Illuminate\Support\Collection<int, Employee>  $employees
     * @param  \Illuminate\Support\Collection<int, Room>  $rooms
     */
    private function seedOccupancies(\Illuminate\Support\Collection $employees, \Illuminate\Support\Collection $rooms): void
    {
        $activeEmployees = $employees->filter(fn (Employee $e) => $e->status === Employee::STATUS_ACTIVE)->values();
        $roomsToAssign = $rooms->take(min($activeEmployees->count(), $rooms->count()));
        $startsAt = CarbonImmutable::now()->subDays(fake()->numberBetween(30, 400));

        foreach ($roomsToAssign as $index => $room) {
            $employee = $activeEmployees->get($index);
            if (! $employee) {
                break;
            }

            if (Occupancy::query()->where('room_id', $room->id)->whereNull('ends_at')->exists()) {
                continue;
            }

            Occupancy::create([
                'room_id' => $room->id,
                'employee_id' => $employee->id,
                'starts_at' => $startsAt->toDateString(),
                'ends_at' => null,
            ]);
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Employee>  $employees
     */
    private function seedContracts(\Illuminate\Support\Collection $employees): void
    {
        $types = ['Permanent', 'Fixed-term', 'Trial'];
        $employeesWithContract = $employees->random(min(14, $employees->count()));

        foreach ($employeesWithContract as $employee) {
            $startsAt = CarbonImmutable::parse($employee->entry_date);
            $endsAt = fake()->boolean(25) ? $startsAt->addYears(fake()->numberBetween(1, 3)) : null;

            Contract::create([
                'employee_id' => $employee->id,
                'type' => $types[array_rand($types)],
                'starts_at' => $startsAt->toDateString(),
                'ends_at' => $endsAt?->toDateString(),
                'notes' => fake()->boolean(20) ? fake()->sentence() : null,
            ]);

            if (fake()->boolean(30)) {
                $secondStartsAt = $endsAt ?? $startsAt->addYears(2);
                Contract::create([
                    'employee_id' => $employee->id,
                    'type' => 'Permanent',
                    'starts_at' => $secondStartsAt->toDateString(),
                    'ends_at' => null,
                    'notes' => null,
                ]);
            }
        }
    }

    /**
     * @param  array<int, array{status: string, weight: int}>  $weighted
     */
    private function weightedRandom(array $weighted): array
    {
        $total = array_sum(array_column($weighted, 'weight'));
        $r = fake()->numberBetween(1, (int) $total);

        foreach ($weighted as $item) {
            $r -= $item['weight'];
            if ($r <= 0) {
                return $item;
            }
        }

        return $weighted[0];
    }
}
