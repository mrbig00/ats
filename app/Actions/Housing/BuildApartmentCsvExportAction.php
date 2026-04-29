<?php

namespace App\Actions\Housing;

use App\Exports\ApartmentExport;
use App\Repositories\ApartmentRepository;

class BuildApartmentCsvExportAction
{
    public function handle(): ApartmentExport
    {
        $apartments = app(ApartmentRepository::class)->allWithRooms();

        return new ApartmentExport($apartments);
    }
}

