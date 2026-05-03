<?php

namespace App\Imports;

use App\Imports\Support\ImportResultCollector;
use App\Models\Person;
use App\Repositories\CandidateRepository;
use App\Repositories\PersonRepository;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class CandidateCsvImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, WithChunkReading, SkipsEmptyRows
{
    use SkipsFailures;

    /**
     * Normalize row values before validation runs.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function prepareForValidation($data, $index): array
    {
        if (! is_array($data)) {
            return [];
        }

        foreach (['person_first_name', 'person_last_name', 'person_email', 'person_phone'] as $key) {
            if (! array_key_exists($key, $data) || $data[$key] === null) {
                continue;
            }

            if (is_string($data[$key])) {
                $data[$key] = trim($data[$key]);
                continue;
            }

            if (is_numeric($data[$key])) {
                $data[$key] = (string) $data[$key];
            }
        }

        return $data;
    }

    public function __construct(
        private readonly CandidateRepository $candidates,
        private readonly PersonRepository $people,
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

        $existing = $this->candidates
            ->findManyByIds($ids)
            ->keyBy('id');

        foreach ($rows as $rowIndex => $row) {
            /** @var array<string, mixed> $row */
            $row = collect($row)->all();
            $rowNumber = (int) $rowIndex + 2; // +1 for 0-index, +1 for heading row

            $id = isset($row['id']) && is_numeric($row['id']) ? (int) $row['id'] : null;

            try {
                $person = $this->resolvePersonFromRow($row);
            } catch (\Throwable $e) {
                $this->collector->addFailure($rowNumber, [$e->getMessage()]);
                continue;
            }

            $attributes = [
                'person_id' => $person->id,
                'position_id' => (int) $row['position_id'],
                'pipeline_stage_id' => (int) $row['pipeline_stage_id'],
                'source' => isset($row['source']) && (string) $row['source'] !== '' ? (string) $row['source'] : null,
                'applied_at' => isset($row['applied_at']) && (string) $row['applied_at'] !== '' ? (string) $row['applied_at'] : null,
                'nationality' => isset($row['nationality']) && (string) $row['nationality'] !== '' ? (string) $row['nationality'] : null,
                'driving_license_category' => isset($row['driving_license_category']) && (string) $row['driving_license_category'] !== '' ? (string) $row['driving_license_category'] : null,
                'has_own_car' => array_key_exists('has_own_car', $row) ? $row['has_own_car'] : null,
                'german_level' => isset($row['german_level']) && (string) $row['german_level'] !== '' ? (string) $row['german_level'] : null,
                'available_from' => isset($row['available_from']) && (string) $row['available_from'] !== '' ? (string) $row['available_from'] : null,
                'housing_needed' => array_key_exists('housing_needed', $row) ? $row['housing_needed'] : null,
            ];

            if ($id !== null && $id > 0 && $existing->has($id)) {
                $this->candidates->updateFromCsv($existing->get($id), $attributes);
                $this->collector->incrementUpdated();
                continue;
            }

            $this->candidates->createFromCsv($attributes);
            $this->collector->incrementCreated();
        }
    }

    /**
     * @param array<string, mixed> $row
     */
    private function resolvePersonFromRow(array $row): Person
    {
        $personId = isset($row['person_id']) && is_numeric($row['person_id']) ? (int) $row['person_id'] : null;
        $personEmail = isset($row['person_email']) && (string) $row['person_email'] !== '' ? trim((string) $row['person_email']) : null;

        $person = null;

        if ($personId !== null && $personId > 0) {
            $person = $this->people->find($personId);
        } elseif ($personEmail !== null) {
            $person = $this->people->findByEmail($personEmail);
        }

        $firstName = isset($row['person_first_name']) && (string) $row['person_first_name'] !== '' ? (string) $row['person_first_name'] : null;
        $lastName = isset($row['person_last_name']) && (string) $row['person_last_name'] !== '' ? (string) $row['person_last_name'] : null;
        $email = isset($row['person_email']) && (string) $row['person_email'] !== '' ? trim((string) $row['person_email']) : null;
        $phone = isset($row['person_phone']) && (string) $row['person_phone'] !== '' ? (string) $row['person_phone'] : null;

        if ($person === null) {
            if ($firstName === null || $lastName === null) {
                throw new \RuntimeException(__('validation.required', ['attribute' => 'person_first_name / person_last_name']));
            }

            return Person::query()->create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
            ]);
        }

        $patch = [];
        if ($firstName !== null) {
            $patch['first_name'] = $firstName;
        }
        if ($lastName !== null) {
            $patch['last_name'] = $lastName;
        }
        if ($email !== null) {
            $patch['email'] = $email;
        }
        if ($phone !== null) {
            $patch['phone'] = $phone;
        }

        if ($patch !== []) {
            $person->update($patch);
        }

        return $person->fresh() ?? $person;
    }

    public function rules(): array
    {
        return [
            '*.id' => ['nullable', 'integer', 'min:1'],
            '*.person_id' => ['nullable', 'integer', 'min:1', 'required_without:person_email'],
            '*.person_first_name' => ['nullable', 'string', 'max:255', 'required_with:person_email'],
            '*.person_last_name' => ['nullable', 'string', 'max:255', 'required_with:person_email'],
            '*.person_email' => ['nullable', 'email', 'max:255', 'required_without:person_id'],
            '*.person_phone' => ['nullable', 'string', 'max:50'],
            '*.position_id' => ['required', 'integer', 'min:1'],
            '*.pipeline_stage_id' => ['required', 'integer', 'min:1'],
            '*.source' => ['nullable', 'string', 'max:255'],
            '*.applied_at' => ['nullable', 'date'],
            '*.nationality' => ['nullable', 'string', 'max:255'],
            '*.driving_license_category' => ['nullable', 'string', 'max:255'],
            '*.has_own_car' => ['nullable', 'boolean'],
            '*.german_level' => ['nullable', 'string', 'max:20'],
            '*.available_from' => ['nullable', 'date'],
            '*.housing_needed' => ['nullable', 'boolean'],
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            'id' => __('common.id'),
            'person_id' => __('common.person'),
            'person_first_name' => __('person.first_name'),
            'person_last_name' => __('person.last_name'),
            'person_email' => __('person.email'),
            'person_phone' => __('person.phone'),
            'position_id' => __('job.title'),
            'pipeline_stage_id' => __('candidate.pipeline_stage'),
            'source' => __('candidate.source'),
            'applied_at' => __('candidate.applied_at'),
            'nationality' => __('candidate.nationality'),
            'driving_license_category' => __('candidate.driving_license_category'),
            'has_own_car' => __('candidate.has_own_car'),
            'german_level' => __('candidate.german_level'),
            'available_from' => __('candidate.available_from'),
            'housing_needed' => __('candidate.housing_needed'),
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

