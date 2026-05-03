<?php

namespace App\Enums;

enum PositionUrgency: string
{
    case Urgent = 'urgent';
    case Medium = 'medium';
    case Good = 'good';

    /**
     * Normalize CSV/spreadsheet wording to a canonical backing value, or a string
     * that still fails `in:urgent,medium,good` when unknown.
     */
    public static function normalizeCsvValue(string $raw): ?string
    {
        $normalized = strtolower(trim($raw));

        if ($normalized === '') {
            return null;
        }

        $canonical = match ($normalized) {
            'high', 'critical' => self::Urgent->value,
            'normal', 'moderate' => self::Medium->value,
            'low' => self::Good->value,
            default => self::tryFrom($normalized)?->value,
        };

        return $canonical ?? $normalized;
    }

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

