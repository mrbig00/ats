<?php

namespace App\Http\Requests\Api\V1;

use App\Data\Candidates\UpdateCandidateProfileData;
use App\Models\Candidate;
use App\Support\CandidateProfileValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCandidateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Candidate $candidate */
        $candidate = $this->route('candidate');

        return $this->user()->can('update', $candidate);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return CandidateProfileValidationRules::sometimesProfileRules();
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return CandidateProfileValidationRules::attributeNames();
    }

    public function toUpdateCandidateProfileData(): UpdateCandidateProfileData
    {
        $v = $this->validated();

        return new UpdateCandidateProfileData($v);
    }
}
