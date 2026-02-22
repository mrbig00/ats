<?php

namespace App\Data\User;

use App\Enums\Role;

readonly class UpdateUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public Role $role,
        public ?string $password = null,
    ) {}
}
