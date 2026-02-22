<?php

namespace App\Actions\User;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListUsersAction
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    /**
     * @return LengthAwarePaginator<User>
     */
    public function handle(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->paginate($perPage);
    }
}
