<?php

namespace App\Actions\Employees;

use App\Data\Employees\ContractData;
use App\Models\Contract;
use App\Repositories\ContractRepository;

class CreateContractAction
{
    public function __construct(
        private ContractRepository $contractRepository,
    ) {}

    public function handle(ContractData $data): Contract
    {
        return $this->contractRepository->create($data);
    }
}
