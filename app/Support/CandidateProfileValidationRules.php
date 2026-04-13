<?php

namespace App\Support;

use App\Enums\GermanLanguageLevel;
use Illuminate\Validation\Rule;

final class CandidateProfileValidationRules
{
    /**
     * @return array<string, list<string|\Illuminate\Validation\Rules\Enum>>
     */
    public static function optionalProfileRules(): array
    {
        return [
            'nationality' => ['nullable', 'string', 'max:120'],
            'driving_license_category' => ['nullable', 'string', 'max:32'],
            'has_own_car' => ['nullable', 'boolean'],
            'german_level' => ['nullable', 'string', Rule::enum(GermanLanguageLevel::class)],
            'available_from' => ['nullable', 'date'],
            'housing_needed' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Rules for Livewire forms using string tri-state '' | '0' | '1' for nullable booleans.
     *
     * @return array<string, list<string|\Illuminate\Validation\Rules\Enum|\Illuminate\Validation\Rules\In>>
     */
    public static function livewireOptionalProfileRules(): array
    {
        $tri = ['nullable', 'string', Rule::in(['', '0', '1'])];

        return [
            'nationality' => ['nullable', 'string', 'max:120'],
            'driving_license_category' => ['nullable', 'string', 'max:32'],
            'has_own_car' => $tri,
            'german_level' => ['nullable', 'string', Rule::in(array_merge([''], GermanLanguageLevel::values()))],
            'available_from' => ['nullable', 'date'],
            'housing_needed' => $tri,
        ];
    }

    /**
     * @return array<string, list<string|\Illuminate\Validation\Rules\Enum>>
     */
    public static function sometimesProfileRules(): array
    {
        return collect(self::optionalProfileRules())
            ->map(fn (array $rules) => array_merge(['sometimes'], $rules))
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public static function attributeNames(): array
    {
        return [
            'nationality' => __('candidate.nationality'),
            'driving_license_category' => __('candidate.driving_license_category'),
            'has_own_car' => __('candidate.has_own_car'),
            'german_level' => __('candidate.german_level'),
            'available_from' => __('candidate.available_from'),
            'housing_needed' => __('candidate.housing_needed'),
        ];
    }
}
