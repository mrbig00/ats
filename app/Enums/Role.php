<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Hr = 'hr';
    case Viewer = 'viewer';

    public function label(): string
    {
        return __('user.role_' . $this->value);
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    public function isHrOrAdmin(): bool
    {
        return $this === self::Admin || $this === self::Hr;
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canEditContent(): bool
    {
        return $this->isHrOrAdmin();
    }
}
