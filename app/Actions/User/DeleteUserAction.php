<?php

namespace App\Actions\User;

use App\Models\User;
use App\Repositories\UserRepository;

class DeleteUserAction
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function handle(User $user): void
    {
        $this->userRepository->delete($user);
    }
}
