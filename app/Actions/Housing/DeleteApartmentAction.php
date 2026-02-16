<?php

namespace App\Actions\Housing;

use App\Models\Apartment;
use App\Repositories\ApartmentRepository;

class DeleteApartmentAction
{
    public function __construct(
        private ApartmentRepository $apartmentRepository,
    ) {}

    public function handle(Apartment $apartment): void
    {
        $this->apartmentRepository->delete($apartment);
    }
}
