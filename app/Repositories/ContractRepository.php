<?php

namespace App\Repositories;

use App\Data\Employees\ContractData;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Collection;

class ContractRepository
{
    public function create(ContractData $data): Contract
    {
        return Contract::query()->create([
            'employee_id' => $data->employeeId,
            'type' => $data->type,
            'starts_at' => $data->startsAt->toDateString(),
            'ends_at' => $data->endsAt?->toDateString(),
            'notes' => $data->notes,
        ]);
    }

    public function update(Contract $contract, ContractData $data): Contract
    {
        $contract->update([
            'type' => $data->type,
            'starts_at' => $data->startsAt->toDateString(),
            'ends_at' => $data->endsAt?->toDateString(),
            'notes' => $data->notes,
        ]);

        return $contract->fresh();
    }

    public function find(int $id): ?Contract
    {
        return Contract::query()->with('employee.person')->find($id);
    }

    /**
     * @return Collection<int, Contract>
     */
    public function getByEmployeeId(int $employeeId): Collection
    {
        return Contract::query()
            ->where('employee_id', $employeeId)
            ->orderByDesc('starts_at')
            ->get();
    }

    public function delete(Contract $contract): void
    {
        $contract->delete();
    }
}
