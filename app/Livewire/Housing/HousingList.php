<?php

namespace App\Livewire\Housing;

use App\Repositories\ApartmentRepository;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class HousingList extends Component
{
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

    public function render()
    {
        return view('livewire.housing.housing-list', [
            'apartments' => $this->apartments,
        ])->layout('layouts.app', ['title' => __('nav.housing')]);
    }
}
