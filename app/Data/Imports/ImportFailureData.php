<?php

namespace App\Data\Imports;

/**
 * A single row-level import failure.
 *
 * @param list<string> $messages
 */
readonly class ImportFailureData
{
    /**
     * @param list<string> $messages
     */
    public function __construct(
        public int $row,
        public array $messages,
    ) {}
}

