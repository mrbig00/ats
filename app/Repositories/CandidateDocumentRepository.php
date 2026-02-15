<?php

namespace App\Repositories;

use App\Models\CandidateDocument;

class CandidateDocumentRepository
{
    public function create(int $candidateId, string $name, string $path, ?string $mimeType, int $size): CandidateDocument
    {
        return CandidateDocument::query()->create([
            'candidate_id' => $candidateId,
            'name' => $name,
            'path' => $path,
            'mime_type' => $mimeType,
            'size' => $size,
        ]);
    }

    public function find(int $id): ?CandidateDocument
    {
        return CandidateDocument::query()->find($id);
    }

    public function delete(CandidateDocument $document): void
    {
        $document->delete();
    }
}
