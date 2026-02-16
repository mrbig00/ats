<?php

namespace App\Livewire\Housing;

use App\Actions\Housing\CreateOccupancyAction;
use App\Data\Housing\OccupancyData;
use App\Models\Room;
use App\Repositories\EmployeeRepository;
use Carbon\CarbonImmutable;
use Livewire\Component;

class AssignOccupancy extends Component
{
    public Room $room;

    public int $employeeId = 0;

    public string $startsAt = '';

    public ?string $endsAt = null;

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Employee>
     */
    public function getEmployeesProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return app(EmployeeRepository::class)->getActiveOrderedByName();
    }

    public function mount(Room $room): void
    {
        $this->room = $room->load('apartment');
        $this->authorize('create', \App\Models\Occupancy::class);
        $this->startsAt = now()->toDateString();
    }

    public function save(): mixed
    {
        $validated = $this->validate([
            'employeeId' => ['required', 'integer', 'exists:employees,id'],
            'startsAt' => ['required', 'date'],
            'endsAt' => ['nullable', 'date', 'after_or_equal:startsAt'],
        ], [], [
            'employeeId' => __('housing.employee'),
            'startsAt' => __('housing.occupancy_starts_at'),
            'endsAt' => __('housing.occupancy_ends_at'),
        ]);

        $data = new OccupancyData(
            roomId: $this->room->id,
            employeeId: (int) $validated['employeeId'],
            startsAt: CarbonImmutable::parse($validated['startsAt']),
            endsAt: isset($validated['endsAt']) && $validated['endsAt'] !== '' ? CarbonImmutable::parse($validated['endsAt']) : null,
        );

        try {
            app(CreateOccupancyAction::class)->handle($data);
        } catch (\DomainException $e) {
            $this->addError('startsAt', $e->getMessage());

            return null;
        }

        $this->dispatch('notify', __('housing.occupancy_created'));

        return $this->redirect(route('housing.apartments.show', $this->room->apartment), navigate: true);
    }

    public function render()
    {
        return view('livewire.housing.assign-occupancy', [
            'employees' => $this->employees,
        ])->layout('layouts.app', ['title' => __('housing.assign_employee')]);
    }
}
