<?php

namespace App\Data\Candidates;

readonly class UploadCandidateDocumentData
{
    public function __construct(
        public string $documentName,
        public string $tempPath,
        public string $tempDisk,
        public string $originalFileName,
        public ?string $absolutePath = null,
    ) {}
}
