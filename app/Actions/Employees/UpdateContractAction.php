<?php

namespace App\Actions\Employees;

use App\Data\Employees\ContractData;
use App\Data\Employees\UpdateContractData;
use App\Models\Contract;
use App\Repositories\ContractRepository;

class UpdateContractAction
{
    public function __construct(
        private ContractRepository $contractRepository,
    ) {}

    public function handle(UpdateContractData $data): Contract
    {
        $contract = $this->contractRepository->find($data->contractId);
        if ($contract === null) {
            throw new \InvalidArgumentException('Contract not found.');
        }

        $contractData = new ContractData(
            employeeId: $contract->employee_id,
            type: $data->type,
            startsAt: $data->startsAt,
            endsAt: $data->endsAt,
            notes: $data->notes,
        );

        return $this->contractRepository->update($contract, $contractData);
    }
}
