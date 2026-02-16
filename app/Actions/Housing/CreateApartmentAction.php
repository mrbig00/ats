<?php

namespace App\Actions\Housing;

use App\Data\Housing\ApartmentData;
use App\Models\Apartment;
use App\Repositories\ApartmentRepository;

class CreateApartmentAction
{
    public function __construct(
        private ApartmentRepository $apartmentRepository,
    ) {}

    public function handle(ApartmentData $data): Apartment
    {
        return $this->apartmentRepository->create($data);
    }
}
