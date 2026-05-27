<?php

namespace App\Actions\Candidates;

use App\Data\Candidates\UploadCandidateDocumentData;
use App\Models\Candidate;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UploadCandidateDocumentAction
{
    public function handle(Candidate $candidate, UploadCandidateDocumentData $data): Media
    {
        if ($data->absolutePath !== null && is_file($data->absolutePath)) {
            $mediaAdder = $candidate->addMedia($data->absolutePath);
        } elseif (config("filesystems.disks.{$data->tempDisk}.driver") === 'local') {
            $mediaAdder = $candidate->addMedia(Storage::disk($data->tempDisk)->path($data->tempPath));
        } else {
            $mediaAdder = $candidate->addMediaFromDisk($data->tempPath, $data->tempDisk);
        }

        return $mediaAdder
            ->usingName($data->documentName)
            ->usingFileName($data->originalFileName)
            ->toMediaCollection('documents');
    }
}
