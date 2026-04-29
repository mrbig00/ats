<?php

namespace App\Exports;

use App\Models\Candidate;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * @implements FromQuery<Candidate>
 */
class CandidateExport implements FromQuery, WithHeadings, WithMapping
{
    /**
     * @param Builder<Candidate> $query
     */
    public function __construct(
        private readonly Builder $query,
    ) {}

    public function headings(): array
    {
        return [
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
        ];
    }

    public function query(): Builder
    {
        return $this->query;
    }

    /**
     * @return array{
     *   0:int,
     *   1:int,
     *   2:?string,
     *   3:?string,
     *   4:?string,
     *   5:?string,
     *   6:int,
     *   7:int,
     *   8:?string,
     *   9:?string,
     *   10:?string,
     *   11:?string,
     *   12:?bool,
     *   13:?string,
     *   14:?string,
     *   15:?bool
     * }
     */
    public function map($row): array
    {
        /** @var Candidate $row */
        return [
            $row->id,
            $row->person_id,
            $row->person?->first_name,
            $row->person?->last_name,
            $row->person?->email,
            $row->person?->phone,
            $row->position_id,
            $row->pipeline_stage_id,
            $row->source,
            $row->applied_at?->toIso8601String(),
            $row->nationality,
            $row->driving_license_category,
            $row->has_own_car,
            $row->german_level?->value,
            $row->available_from?->toDateString(),
            $row->housing_needed,
        ];
    }
}

