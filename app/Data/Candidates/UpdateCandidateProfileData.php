<?php

namespace App\Data\Candidates;

use App\Enums\GermanLanguageLevel;

/**
 * Partial update for candidate profile extension fields.
 *
 * Validation (all optional unless creating a candidate, where position/stage stay required elsewhere):
 * - nationality: optional string, max 120
 * - driving_license_category: optional string, max 32
 * - has_own_car: optional boolean (null allowed in storage = unknown)
 * - german_level: optional, must be a {@see GermanLanguageLevel} value when present
 * - available_from: optional date
 * - housing_needed: optional boolean (null allowed in storage = unknown)
 *
 * @see \App\Http\Requests\Api\V1\UpdateCandidateProfileRequest
 * @see \App\Http\Requests\Api\V1\StoreCandidateRequest
 */
readonly class UpdateCandidateProfileData
{
    /**
     * @param  array<string, mixed>  $attributes  Only keys listed in {@see self::allowedKeys()}; values are DB-ready (strings, bools, date strings).
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
