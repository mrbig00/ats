<?php

namespace App\Imports;

use App\Enums\PositionUrgency;
use App\Imports\Support\ImportResultCollector;
use App\Repositories\PositionRepository;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class PositionCsvImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, WithChunkReading, SkipsEmptyRows
{
    use SkipsFailures;

    public function __construct(
        private readonly PositionRepository $positions,
        private readonly ImportResultCollector $collector,
    ) {}

    /**
     * @param array<string, mixed>|mixed $data
     * @return array<string, mixed>
     */
    public function prepareForValidation($data, $index): array
    {
        if (! is_array($data)) {
            return [];
        }

        if (array_key_exists('urgency', $data) && $data['urgency'] !== null && (string) $data['urgency'] !== '') {
            $data['urgency'] = PositionUrgency::normalizeCsvValue((string) $data['urgency']);
        }

        return $data;
    }

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

        $existing = $this->positions
            ->findManyByIds($ids)
            ->keyBy('id');

        foreach ($rows as $row) {
            /** @var array<string, mixed> $row */
            $id = isset($row['id']) && is_numeric($row['id']) ? (int) $row['id'] : null;

            $attributes = [
                'title' => (string) $row['title'],
                'description' => isset($row['description']) && (string) $row['description'] !== '' ? (string) $row['description'] : null,
                'status' => (string) $row['status'],
                'urgency' => isset($row['urgency']) && (string) $row['urgency'] !== ''
                    ? PositionUrgency::normalizeCsvValue((string) $row['urgency'])
                    : null,
                'opens_at' => isset($row['opens_at']) && (string) $row['opens_at'] !== '' ? (string) $row['opens_at'] : null,
                'closes_at' => isset($row['closes_at']) && (string) $row['closes_at'] !== '' ? (string) $row['closes_at'] : null,
            ];

            if ($id !== null && $id > 0 && $existing->has($id)) {
                $this->positions->updateFromCsv($existing->get($id), $attributes);
                $this->collector->incrementUpdated();
                continue;
            }

            $this->positions->createFromCsv($attributes);
            $this->collector->incrementCreated();
        }
    }

    public function rules(): array
    {
        return [
            '*.id' => ['nullable', 'integer', 'min:1'],
            '*.title' => ['required', 'string', 'max:255'],
            '*.description' => ['nullable', 'string'],
            '*.status' => ['required', 'string', 'in:open,closed'],
            '*.urgency' => ['nullable', 'string', 'in:urgent,medium,good'],
            '*.opens_at' => ['nullable', 'date'],
            '*.closes_at' => ['nullable', 'date'],
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            'id' => __('common.id'),
            'title' => __('job.title'),
            'description' => __('common.description'),
            'status' => __('common.status'),
            'urgency' => __('job.urgency'),
            'opens_at' => __('job.opens_at'),
            'closes_at' => __('job.closes_at'),
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

