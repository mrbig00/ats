<?php

namespace App\Livewire\Housing;

use App\Actions\Housing\ImportApartmentsCsvAction;
use App\Data\Imports\CsvImportFileData;
use App\Repositories\ApartmentRepository;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class HousingList extends Component
{
    use WithFileUploads;

    public bool $csvImportOpen = false;

    public mixed $csvFile = null;

    public ?array $csvImportResult = null;

    /**
     * @return Collection<int, \App\Models\Apartment>
     */
    public function getApartmentsProperty(): Collection
    {
        return app(ApartmentRepository::class)->all();
    }

    public function mount(): void
    {
        $this->authorize('viewAny', \App\Models\Apartment::class);
    }

    public function openCsvImport(): void
    {
        $this->authorize('importCsv', \App\Models\Apartment::class);
        $this->csvImportOpen = true;
        $this->csvImportResult = null;
    }

    public function closeCsvImport(): void
    {
        $this->csvImportOpen = false;
        $this->reset(['csvFile']);
    }

    public function importCsv(): void
    {
        $this->authorize('importCsv', \App\Models\Apartment::class);

        $this->validate([
            'csvFile' => ['required', 'file', 'max:51200', 'mimes:csv,txt'],
        ], [], [
            'csvFile' => __('common.csv'),
        ]);

        $path = $this->csvFile->store('csv-imports/apartments', 'local');
        $result = app(ImportApartmentsCsvAction::class)->handle(new CsvImportFileData(
            path: $path,
            originalName: $this->csvFile->getClientOriginalName(),
        ));

        $this->csvImportResult = $result->toArray();
        $this->reset(['csvFile']);
        $this->dispatch('notify', __('common.csv_import_completed'));
    }

    public function render()
    {
        return view('livewire.housing.housing-list', [
            'apartments' => $this->apartments,
        ])->layout('layouts.app', ['title' => __('nav.housing')]);
    }
}
