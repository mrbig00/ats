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

    /**
     * @return Collection<int, Apartment>
     */
    public function allWithRooms(): Collection
    {
        return Apartment::query()->with('rooms')->orderBy('name')->get();
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

    /**
     * @param list<int> $ids
     * @return Collection<int, Apartment>
     */
    public function findManyByIds(array $ids): Collection
    {
        if ($ids === []) {
            return new Collection();
        }

        return Apartment::query()->whereIn('id', $ids)->get();
    }

    /**
     * @param array{name:string,address:?string,notes:?string} $attributes
     */
    public function createFromCsv(array $attributes): Apartment
    {
        return Apartment::query()->create($attributes);
    }

    /**
     * @param array{name:string,address:?string,notes:?string} $attributes
     */
    public function updateFromCsv(Apartment $apartment, array $attributes): Apartment
    {
        $apartment->update($attributes);

        return $apartment->fresh() ?? $apartment;
    }
}
