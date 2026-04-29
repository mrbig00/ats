<?php

namespace App\Imports;

use App\Imports\Support\ImportResultCollector;
use App\Repositories\ApartmentRepository;
use App\Repositories\RoomRepository;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class ApartmentCsvImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, WithChunkReading, SkipsEmptyRows
{
    use SkipsFailures;

    public function __construct(
        private readonly ApartmentRepository $apartments,
        private readonly RoomRepository $rooms,
        private readonly ImportResultCollector $collector,
    ) {}

    public function collection(Collection $rows): void
    {
        $this->collector->addTotalRows($rows->count());

        /** @var list<int> $ids */
        $ids = $rows
            ->pluck('id')
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $existing = $this->apartments
            ->findManyByIds($ids)
            ->keyBy('id');

        /** @var list<int> $roomIds */
        $roomIds = $rows
            ->pluck('room_id')
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $existingRooms = $this->rooms
            ->findManyByIds($roomIds)
            ->keyBy('id');

        foreach ($rows as $row) {
            /** @var array<string, mixed> $row */
            $id = isset($row['id']) && is_numeric($row['id']) ? (int) $row['id'] : null;

            $attributes = [
                'name' => (string) $row['name'],
                'address' => isset($row['address']) && (string) $row['address'] !== '' ? (string) $row['address'] : null,
                'notes' => isset($row['notes']) && (string) $row['notes'] !== '' ? (string) $row['notes'] : null,
            ];

            $apartment = null;
            if ($id !== null && $id > 0 && $existing->has($id)) {
                $apartment = $this->apartments->updateFromCsv($existing->get($id), $attributes);
                $this->collector->incrementUpdated();
            } else {
                $apartment = $this->apartments->createFromCsv($attributes);
                $this->collector->incrementCreated();
            }

            $roomId = isset($row['room_id']) && is_numeric($row['room_id']) ? (int) $row['room_id'] : null;
            $roomName = isset($row['room_name']) && (string) $row['room_name'] !== '' ? (string) $row['room_name'] : null;
            $roomNotes = isset($row['room_notes']) && (string) $row['room_notes'] !== '' ? (string) $row['room_notes'] : null;

            if ($roomId === null && $roomName === null && $roomNotes === null) {
                continue;
            }

            $roomAttributes = [
                'apartment_id' => $apartment->id,
                'name' => $roomName ?? '',
                'notes' => $roomNotes,
            ];

            if ($roomId !== null && $roomId > 0 && $existingRooms->has($roomId)) {
                $this->rooms->updateFromCsv($existingRooms->get($roomId), $roomAttributes);
                continue;
            }

            $this->rooms->createFromCsv($roomAttributes);
        }
    }

    public function rules(): array
    {
        return [
            '*.id' => ['nullable', 'integer', 'min:1'],
            '*.name' => ['required', 'string', 'max:255'],
            '*.address' => ['nullable', 'string'],
            '*.notes' => ['nullable', 'string'],
            '*.room_id' => ['nullable', 'integer', 'min:1'],
            '*.room_name' => ['nullable', 'string', 'max:255', 'required_with:room_id,room_notes'],
            '*.room_notes' => ['nullable', 'string'],
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            'id' => __('common.id'),
            'name' => __('housing.apartment_name'),
            'address' => __('housing.address'),
            'notes' => __('common.notes'),
            'room_id' => __('common.id'),
            'room_name' => __('housing.room_name'),
            'room_notes' => __('common.notes'),
        ];
    }

    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $this->collector->addFailure($failure->row(), $failure->errors());
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }
}

