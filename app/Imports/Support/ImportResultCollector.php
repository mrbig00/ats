<?php

namespace App\Imports\Support;

use App\Data\Imports\ImportFailureData;
use App\Data\Imports\ImportResultData;

class ImportResultCollector
{
    private int $totalRows = 0;

    private int $createdCount = 0;

    private int $updatedCount = 0;

    /** @var list<ImportFailureData> */
    private array $failures = [];

    public function addTotalRows(int $count): void
    {
        $this->totalRows += $count;
    }

    public function incrementCreated(): void
    {
        $this->createdCount++;
    }

    public function incrementUpdated(): void
    {
        $this->updatedCount++;
    }

    /**
     * @param list<string> $messages
     */
    public function addFailure(int $row, array $messages): void
    {
        $this->failures[] = new ImportFailureData($row, $messages);
    }

    public function result(): ImportResultData
    {
        return new ImportResultData(
            totalRows: $this->totalRows,
            createdCount: $this->createdCount,
            updatedCount: $this->updatedCount,
            failedCount: count($this->failures),
            failures: $this->failures,
        );
    }
}

