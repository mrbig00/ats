<?php

namespace App\Actions\User;

use App\Data\User\UserData;
use App\Models\User;
use App\Repositories\UserRepository;

class CreateUserAction
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function handle(UserData $data): User
    {
        return $this->userRepository->create($data);
    }
}
