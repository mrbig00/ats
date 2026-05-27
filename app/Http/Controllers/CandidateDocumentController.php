<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CandidateDocumentController extends Controller
{
    public function download(Candidate $candidate, Media $media): RedirectResponse|StreamedResponse
    {
        $this->authorize('view', $candidate);

        if ($media->model_id !== $candidate->id || $media->model_type !== $candidate->getMorphClass()) {
            abort(404);
        }

        if ($media->collection_name !== 'documents') {
            abort(404);
        }

        if (config("filesystems.disks.{$media->disk}.driver") === 's3') {
            return redirect($media->getTemporaryUrl(now()->addMinutes(5)));
        }

        if (! Storage::disk($media->disk)->exists($media->getPathRelativeToRoot())) {
            abort(404);
        }

        return Storage::disk($media->disk)->download(
            $media->getPathRelativeToRoot(),
            $media->name,
        );
    }
}
