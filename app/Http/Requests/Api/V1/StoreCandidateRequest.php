<?php

namespace App\Http\Requests\Api\V1;

use App\Data\Candidates\CandidateData;
use App\Data\Candidates\PersonData;
use App\Enums\GermanLanguageLevel;
use App\Support\CandidateProfileValidationRules;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Candidate::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'position_id' => ['required', 'integer', 'exists:positions,id'],
            'pipeline_stage_id' => ['required', 'integer', 'exists:pipeline_stages,id'],
            'source' => ['nullable', 'string', 'max:100'],
            'applied_at' => ['nullable', 'date'],
        ] + CandidateProfileValidationRules::optionalProfileRules();
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => __('candidate.first_name'),
            'last_name' => __('candidate.last_name'),
            'email' => __('candidate.email'),
            'phone' => __('candidate.phone'),
            'position_id' => __('candidate.position'),
            'pipeline_stage_id' => __('candidate.stage'),
            'source' => __('candidate.source'),
            'applied_at' => __('candidate.applied_at'),
        ] + CandidateProfileValidationRules::attributeNames();
    }

    public function toPersonData(): PersonData
    {
        $v = $this->validated();

        return new PersonData(
            firstName: $v['first_name'],
            lastName: $v['last_name'],
            email: $v['email'] ?? null,
            phone: $v['phone'] ?? null,
        );
    }

    public function toCandidateData(): CandidateData
    {
        $v = $this->validated();

        return new CandidateData(
            personId: 0,
            positionId: (int) $v['position_id'],
            pipelineStageId: (int) $v['pipeline_stage_id'],
            source: $v['source'] ?? null,
            appliedAt: isset($v['applied_at']) ? CarbonImmutable::parse($v['applied_at']) : null,
            nationality: $v['nationality'] ?? null,
            drivingLicenseCategory: $v['driving_license_category'] ?? null,
            hasOwnCar: $v['has_own_car'] ?? null,
            germanLevel: array_key_exists('german_level', $v) && $v['german_level'] !== null
                ? GermanLanguageLevel::from($v['german_level'])
                : null,
            availableFrom: isset($v['available_from']) ? CarbonImmutable::parse($v['available_from']) : null,
            housingNeeded: $v['housing_needed'] ?? null,
        );
    }
}
