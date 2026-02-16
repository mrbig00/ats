<?php

namespace App\Actions\Housing;

use App\Data\Housing\ApartmentData;
use App\Models\Apartment;
use App\Repositories\ApartmentRepository;

class UpdateApartmentAction
{
    public function __construct(
        private ApartmentRepository $apartmentRepository,
    ) {}

    public function handle(Apartment $apartment, ApartmentData $data): Apartment
    {
        return $this->apartmentRepository->update($apartment, $data);
    }
}
