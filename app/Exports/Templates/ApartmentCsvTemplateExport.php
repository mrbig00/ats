<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;

class ApartmentCsvTemplateExport implements FromArray
{
    public function array(): array
    {
        return [
            ['id', 'name', 'address', 'notes', 'room_id', 'room_name', 'room_notes'],
            [123, 'Apartment Example', 'Example street 1', 'Example notes', 456, 'Room 1', 'Room notes'],
            [123, 'Apartment Example', 'Example street 1', 'Example notes', 457, 'Room 2', 'Second room notes'],
        ];
    }
}

