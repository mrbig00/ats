<?php

namespace App\Exports;

use App\Models\Apartment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * @implements FromArray<int, array<int, mixed>>
 */
class ApartmentExport implements FromArray, WithHeadings
{
    /** @var list<array<int, mixed>> */
    private array $rows;

    /**
     * @param Collection<int, Apartment> $apartments
     */
    public function __construct(
        private readonly Collection $apartments,
    ) {
        $this->rows = $this->buildRows($apartments);
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'address',
            'notes',
            'room_id',
            'room_name',
            'room_notes',
        ];
    }

    /**
     * @return list<array<int, mixed>>
     */
    public function array(): array
    {
        return $this->rows;
    }

    /**
     * @param Collection<int, Apartment> $apartments
     * @return list<array<int, mixed>>
     */
    private function buildRows(Collection $apartments): array
    {
        $rows = [];

        foreach ($apartments as $apartment) {
            if ($apartment->relationLoaded('rooms') && $apartment->rooms->isNotEmpty()) {
                foreach ($apartment->rooms as $room) {
                    $rows[] = [
                        $apartment->id,
                        $apartment->name,
                        $apartment->address,
                        $apartment->notes,
                        $room->id,
                        $room->name,
                        $room->notes,
                    ];
                }
                continue;
            }

            $rows[] = [
                $apartment->id,
                $apartment->name,
                $apartment->address,
                $apartment->notes,
                null,
                null,
                null,
            ];
        }

        return $rows;
    }
}

