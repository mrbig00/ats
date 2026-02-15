<?php

namespace App\Repositories;

use App\Data\Candidates\PersonData;
use App\Models\Person;

class PersonRepository
{
    public function create(PersonData $data): Person
    {
        return Person::query()->create([
            'first_name' => $data->firstName,
            'last_name' => $data->lastName,
            'email' => $data->email,
            'phone' => $data->phone,
        ]);
    }

    public function find(int $id): ?Person
    {
        return Person::query()->find($id);
    }
}
