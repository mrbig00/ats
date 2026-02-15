<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateDocument;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class CandidateDocumentController extends Controller
{
    public function download(Candidate $candidate, CandidateDocument $document): Response
    {
        $this->authorize('view', $candidate);
        if ($document->candidate_id !== $candidate->id) {
            abort(404);
        }
        $path = $document->getStoragePath();
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->download($path, $document->name);
    }
}
