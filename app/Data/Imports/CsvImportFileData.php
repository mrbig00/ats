<?php

namespace App\Data\Imports;

readonly class CsvImportFileData
{
    public function __construct(
        public string $path,
        public string $originalName,
    ) {}
}

