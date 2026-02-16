<?php

namespace App\Repositories;

use App\Data\Housing\ApartmentData;
use App\Models\Apartment;
use Illuminate\Database\Eloquent\Collection;

/**
 * @return Collection<int, Apartment>
 */
class ApartmentRepository
{
    public function all(): Collection
    {
        return Apartment::query()->withCount('rooms')->orderBy('name')->get();
    }

    public function find(int $id): ?Apartment
    {
        return Apartment::query()->with('rooms.occupancies.employee.person')->withCount('rooms')->find($id);
    }

    public function create(ApartmentData $data): Apartment
    {
        return Apartment::query()->create([
            'name' => $data->name,
            'address' => $data->address,
            'notes' => $data->notes,
        ]);
    }

    public function update(Apartment $apartment, ApartmentData $data): Apartment
    {
        $apartment->update([
            'name' => $data->name,
            'address' => $data->address,
            'notes' => $data->notes,
        ]);

        return $apartment->fresh();
    }

    public function delete(Apartment $apartment): void
    {
        $apartment->delete();
    }
}
