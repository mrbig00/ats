<?php

namespace App\Actions\User;

use App\Data\User\UpdateUserData;
use App\Models\User;
use App\Repositories\UserRepository;

class UpdateUserAction
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function handle(User $user, UpdateUserData $data): User
    {
        return $this->userRepository->update($user, $data);
    }
}
