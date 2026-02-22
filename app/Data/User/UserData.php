<?php

namespace App\Data\User;

use App\Enums\Role;

readonly class UserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public Role $role,
    ) {}
}
