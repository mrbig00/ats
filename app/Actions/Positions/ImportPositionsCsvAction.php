<?php

namespace App\Actions\Positions;

use App\Data\Imports\CsvImportFileData;
use App\Data\Imports\ImportResultData;
use App\Imports\PositionCsvImport;
use App\Imports\Support\ImportResultCollector;
use App\Repositories\PositionRepository;
use Maatwebsite\Excel\Facades\Excel;

class ImportPositionsCsvAction
{
    public function handle(CsvImportFileData $file): ImportResultData
    {
        $collector = new ImportResultCollector();
        $import = new PositionCsvImport(
            positions: app(PositionRepository::class),
            collector: $collector,
        );

        Excel::import($import, $file->path, 'local', \Maatwebsite\Excel\Excel::CSV);

        return $collector->result();
    }
}

