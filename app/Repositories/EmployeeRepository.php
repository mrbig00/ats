<?php

namespace App\Repositories;

use App\Data\Employees\EmployeeFilterData;
use App\Models\Employee;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EmployeeRepository
{
    public function create(int $personId, \Carbon\CarbonImmutable $entryDate): Employee
    {
        return Employee::query()->create([
            'person_id' => $personId,
            'status' => Employee::STATUS_ACTIVE,
            'entry_date' => $entryDate->toDateString(),
            'exit_date' => null,
        ]);
    }

    public function find(int $id): ?Employee
    {
        return Employee::query()->with('person')->find($id);
    }

    public function findByPersonId(int $personId): ?Employee
    {
        return Employee::query()->where('person_id', $personId)->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Employee>
     */
    public function getActiveOrderedByName(): \Illuminate\Database\Eloquent\Collection
    {
        return Employee::query()
            ->where('status', Employee::STATUS_ACTIVE)
            ->with('person')
            ->join('persons', 'employees.person_id', '=', 'persons.id')
            ->orderBy('persons.last_name')
            ->orderBy('persons.first_name')
            ->select('employees.*')
            ->get();
    }

    /**
     * @return LengthAwarePaginator<Employee>
     */
    public function paginate(EmployeeFilterData $filters): LengthAwarePaginator
    {
        $query = Employee::query()->with('person');

        if ($filters->search !== null && $filters->search !== '') {
            $search = '%'.addcslashes($filters->search, '%_').'%';
            $query->whereHas('person', function (Builder $q) use ($search) {
                $q->where('first_name', 'ilike', $search)
                    ->orWhere('last_name', 'ilike', $search)
                    ->orWhere('email', 'ilike', $search);
            });
        }

        if ($filters->status !== null && $filters->status !== '') {
            $query->where('status', $filters->status);
        }

        $direction = strtolower($filters->sortDirection) === 'desc' ? 'desc' : 'asc';
        $sortField = $this->sortFieldColumn($filters->sortField);
        if ($sortField === 'persons.last_name') {
            $query->join('persons', 'employees.person_id', '=', 'persons.id')
                ->orderBy('persons.last_name', $direction)
                ->orderBy('persons.first_name', $direction)
                ->select('employees.*');
        } else {
            $query->orderBy($sortField, $direction);
        }

        return $query->paginate($filters->perPage);
    }

    public function updateStatus(Employee $employee, string $status, ?\Carbon\CarbonImmutable $exitDate): Employee
    {
        $employee->update([
            'status' => $status,
            'exit_date' => $exitDate?->toDateString(),
        ]);

        return $employee->fresh();
    }

    public function updateEntryDate(Employee $employee, \Carbon\CarbonImmutable $entryDate): Employee
    {
        $employee->update(['entry_date' => $entryDate->toDateString()]);

        return $employee->fresh();
    }

    public function countActive(): int
    {
        return Employee::query()->where('status', Employee::STATUS_ACTIVE)->count();
    }

    /**
     * Employees with exit_date within the next $days days (including today).
     *
     * @return Collection<int, Employee>
     */
    public function getUpcomingDepartures(int $days): Collection
    {
        $from = CarbonImmutable::today();
        $to = $from->addDays($days);

        return Employee::query()
            ->where('status', Employee::STATUS_ACTIVE)
            ->whereNotNull('exit_date')
            ->where('exit_date', '>=', $from->toDateString())
            ->where('exit_date', '<=', $to->toDateString())
            ->with('person')
            ->orderBy('exit_date')
            ->get();
    }

    private function sortFieldColumn(string $field): string
    {
        return match ($field) {
            'name' => 'persons.last_name',
            'entry_date' => 'entry_date',
            'exit_date' => 'exit_date',
            'status' => 'status',
            default => 'entry_date',
        };
    }
}
