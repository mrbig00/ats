<?php

namespace App\Data\Imports;

/**
 * @param list<ImportFailureData> $failures
 */
readonly class ImportResultData
{
    /**
     * @param list<ImportFailureData> $failures
     */
    public function __construct(
        public int $totalRows,
        public int $createdCount,
        public int $updatedCount,
        public int $failedCount,
        public array $failures = [],
    ) {}

    /**
     * @return array{
     *   totalRows:int,
     *   createdCount:int,
     *   updatedCount:int,
     *   failedCount:int,
     *   failures:list<array{row:int,messages:list<string>}>
     * }
     */
    public function toArray(): array
    {
        return [
            'totalRows' => $this->totalRows,
            'createdCount' => $this->createdCount,
            'updatedCount' => $this->updatedCount,
            'failedCount' => $this->failedCount,
            'failures' => array_map(fn (ImportFailureData $f) => [
                'row' => $f->row,
                'messages' => $f->messages,
            ], $this->failures),
        ];
    }
}

