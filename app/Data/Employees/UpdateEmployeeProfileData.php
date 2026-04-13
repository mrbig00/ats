<?php

namespace App\Data\Employees;

/**
 * Partial update for employee profile fields (aligned with candidate profile extension).
 *
 * @see \App\Support\CandidateProfileValidationRules
 */
readonly class UpdateEmployeeProfileData
{
    /**
     * @param  array<string, mixed>  $attributes  Only keys in {@see self::allowedKeys()}; DB-ready values.
     */
    public function __construct(
        public array $attributes,
    ) {
        $unknown = array_diff(array_keys($attributes), self::allowedKeys());
        if ($unknown !== []) {
            throw new \InvalidArgumentException('Unknown profile attribute keys: '.implode(', ', $unknown));
        }
    }

    /**
     * @return list<string>
     */
    public static function allowedKeys(): array
    {
        return [
            'nationality',
            'driving_license_category',
            'has_own_car',
            'german_level',
            'available_from',
            'housing_needed',
        ];
    }
}
