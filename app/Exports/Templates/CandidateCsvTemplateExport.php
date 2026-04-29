<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;

class CandidateCsvTemplateExport implements FromArray
{
    public function array(): array
    {
        return [
            [
                'id',
                'person_id',
                'person_first_name',
                'person_last_name',
                'person_email',
                'person_phone',
                'position_id',
                'pipeline_stage_id',
                'source',
                'applied_at',
                'nationality',
                'driving_license_category',
                'has_own_car',
                'german_level',
                'available_from',
                'housing_needed',
            ],
            [
                123,
                1,
                'Jane',
                'Doe',
                'jane.doe@example.com',
                '+49123456789',
                1,
                1,
                'referral',
                '2026-01-01T12:00:00+00:00',
                'DE',
                'B',
                true,
                'a2',
                '2026-02-01',
                false,
            ],
        ];
    }
}

