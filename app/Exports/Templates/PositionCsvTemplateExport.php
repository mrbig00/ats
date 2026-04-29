<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;

class PositionCsvTemplateExport implements FromArray
{
    public function array(): array
    {
        return [
            ['id', 'title', 'description', 'status', 'urgency', 'opens_at', 'closes_at'],
            [123, 'Job title', 'Job description', 'open', 'high', '2026-01-01', '2026-02-01'],
        ];
    }
}

