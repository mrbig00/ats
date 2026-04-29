<?php

namespace App\Exports;

use App\Models\Position;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * @implements FromQuery<Position>
 */
class PositionExport implements FromQuery, WithHeadings, WithMapping
{
    /**
     * @param Builder<Position> $query
     */
    public function __construct(
        private readonly Builder $query,
    ) {}

    public function headings(): array
    {
        return [
            'id',
            'title',
            'description',
            'status',
            'urgency',
            'opens_at',
            'closes_at',
        ];
    }

    public function query(): Builder
    {
        return $this->query;
    }

    /**
     * @return array{0:int,1:string,2:?string,3:string,4:?string,5:?string,6:?string}
     */
    public function map($row): array
    {
        /** @var Position $row */
        return [
            $row->id,
            $row->title,
            $row->description,
            $row->status,
            $row->urgency?->value,
            $row->opens_at?->toDateString(),
            $row->closes_at?->toDateString(),
        ];
    }
}

