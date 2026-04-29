<?php

namespace App\Http\Controllers;

use App\Actions\Candidates\BuildCandidateCsvExportAction;
use App\Actions\Housing\BuildApartmentCsvExportAction;
use App\Actions\Positions\BuildPositionCsvExportAction;
use App\Data\Candidates\CandidateFilterData;
use App\Data\Positions\PositionFilterData;
use App\Models\Apartment;
use App\Models\Candidate;
use App\Models\Position;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CsvExportController extends Controller
{
    public function candidates(Request $request): BinaryFileResponse
    {
        $this->authorize('exportCsv', Candidate::class);

        $filters = new CandidateFilterData(
            search: trim((string) $request->query('q', '')) !== '' ? trim((string) $request->query('q')) : null,
            pipelineStageId: $request->query('stage') !== null ? (int) $request->query('stage') : null,
            positionId: $request->query('position') !== null ? (int) $request->query('position') : null,
            appliedFrom: trim((string) $request->query('from', '')) !== '' ? trim((string) $request->query('from')) : null,
            appliedTo: trim((string) $request->query('to', '')) !== '' ? trim((string) $request->query('to')) : null,
            sortField: (string) $request->query('sort', 'created_at'),
            sortDirection: (string) $request->query('dir', 'asc'),
            perPage: 15,
            includeArchived: (bool) $request->boolean('archived', false),
        );

        $export = app(BuildCandidateCsvExportAction::class)->handle($filters);

        return Excel::download($export, 'candidates.csv');
    }

    public function positions(Request $request): BinaryFileResponse
    {
        $this->authorize('exportCsv', Position::class);

        $filters = new PositionFilterData(
            status: trim((string) $request->query('status', '')) !== '' ? (string) $request->query('status') : null,
            search: trim((string) $request->query('q', '')) !== '' ? trim((string) $request->query('q')) : null,
            sortField: (string) $request->query('sort', 'created_at'),
            sortDirection: (string) $request->query('dir', 'asc'),
            perPage: 15,
            includeArchived: (bool) $request->boolean('archived', false),
        );

        $export = app(BuildPositionCsvExportAction::class)->handle($filters);

        return Excel::download($export, 'jobs.csv');
    }

    public function apartments(Request $request): BinaryFileResponse
    {
        $this->authorize('exportCsv', Apartment::class);

        $export = app(BuildApartmentCsvExportAction::class)->handle();

        return Excel::download($export, 'apartments.csv');
    }
}

