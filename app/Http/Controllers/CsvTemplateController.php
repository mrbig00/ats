<?php

namespace App\Http\Controllers;

use App\Exports\Templates\ApartmentCsvTemplateExport;
use App\Exports\Templates\CandidateCsvTemplateExport;
use App\Exports\Templates\PositionCsvTemplateExport;
use App\Models\Apartment;
use App\Models\Candidate;
use App\Models\Position;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CsvTemplateController extends Controller
{
    public function candidates(Request $request): BinaryFileResponse
    {
        $this->authorize('downloadCsvTemplate', Candidate::class);

        return Excel::download(new CandidateCsvTemplateExport(), 'candidates-template.csv');
    }

    public function positions(Request $request): BinaryFileResponse
    {
        $this->authorize('downloadCsvTemplate', Position::class);

        return Excel::download(new PositionCsvTemplateExport(), 'jobs-template.csv');
    }

    public function apartments(Request $request): BinaryFileResponse
    {
        $this->authorize('downloadCsvTemplate', Apartment::class);

        return Excel::download(new ApartmentCsvTemplateExport(), 'apartments-template.csv');
    }
}

