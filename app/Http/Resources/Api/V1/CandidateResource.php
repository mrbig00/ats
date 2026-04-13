<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Candidate */
class CandidateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'position_id' => $this->position_id,
            'pipeline_stage_id' => $this->pipeline_stage_id,
            'source' => $this->source,
            'applied_at' => $this->applied_at?->toIso8601String(),
            'nationality' => $this->nationality,
            'driving_license_category' => $this->driving_license_category,
            'has_own_car' => $this->has_own_car,
            'housing_needed' => $this->housing_needed,
            'available_from' => $this->available_from?->toDateString(),
            'german_level' => $this->german_level !== null
                ? [
                    'value' => $this->german_level->value,
                    'label' => $this->german_level->label(),
                ]
                : null,
            'person' => [
                'first_name' => $this->person->first_name,
                'last_name' => $this->person->last_name,
                'email' => $this->person->email,
                'phone' => $this->person->phone,
            ],
            'url' => route('candidates.show', $this->id),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
