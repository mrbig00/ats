<?php

namespace App\Repositories;

use App\Data\User\UpdateUserData;
use App\Data\User\UserData;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    /**
     * @return LengthAwarePaginator<User>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * @return Collection<int, User>
     */
    public function all(): Collection
    {
        return User::query()->orderBy('name')->get();
    }

    public function create(UserData $data): User
    {
        return User::query()->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => bcrypt($data->password),
            'role' => $data->role,
        ]);
    }

    public function update(User $user, UpdateUserData $data): User
    {
        $attributes = [
            'name' => $data->name,
            'email' => $data->email,
            'role' => $data->role,
        ];

        if ($data->password !== null && $data->password !== '') {
            $attributes['password'] = bcrypt($data->password);
        }

        $user->update($attributes);

        return $user->fresh();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
