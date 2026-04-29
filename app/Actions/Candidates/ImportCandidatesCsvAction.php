<?php

namespace App\Actions\Candidates;

use App\Data\Imports\CsvImportFileData;
use App\Data\Imports\ImportResultData;
use App\Imports\CandidateCsvImport;
use App\Imports\Support\ImportResultCollector;
use App\Repositories\CandidateRepository;
use App\Repositories\PersonRepository;
use Maatwebsite\Excel\Facades\Excel;

class ImportCandidatesCsvAction
{
    public function handle(CsvImportFileData $file): ImportResultData
    {
        $collector = new ImportResultCollector();
        $import = new CandidateCsvImport(
            candidates: app(CandidateRepository::class),
            people: app(PersonRepository::class),
            collector: $collector,
        );

        Excel::import($import, $file->path, 'local', \Maatwebsite\Excel\Excel::CSV);

        return $collector->result();
    }
}

