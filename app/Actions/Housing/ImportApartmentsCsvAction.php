<?php

namespace App\Actions\Housing;

use App\Data\Imports\CsvImportFileData;
use App\Data\Imports\ImportResultData;
use App\Imports\ApartmentCsvImport;
use App\Imports\Support\ImportResultCollector;
use App\Repositories\ApartmentRepository;
use App\Repositories\RoomRepository;
use Maatwebsite\Excel\Facades\Excel;

class ImportApartmentsCsvAction
{
    public function handle(CsvImportFileData $file): ImportResultData
    {
        $collector = new ImportResultCollector();
        $import = new ApartmentCsvImport(
            apartments: app(ApartmentRepository::class),
            rooms: app(RoomRepository::class),
            collector: $collector,
        );

        Excel::import($import, $file->path, 'local', \Maatwebsite\Excel\Excel::CSV);

        return $collector->result();
    }
}

