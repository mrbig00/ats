<?php

namespace App\Enums;

enum PositionUrgency: string
{
    case Urgent = 'urgent';
    case Medium = 'medium';
    case Good = 'good';

    public function label(): string
    {
        return __('job.urgency_' . $this->value);
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Urgent => 'red',
            self::Medium => 'amber',
            self::Good => 'green',
        };
    }
}

