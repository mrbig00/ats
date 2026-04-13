<?php

namespace App\Http\Resources\Api\V1;

use App\Support\CandidateActivityPresentation;
use App\Support\CandidatePipelineStageActivity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Candidate */
class CandidateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $candidate = $this->resource;

        $changeHistory = collect(CandidateActivityPresentation::changeHistoryRows($candidate))
            ->map(fn (array $row) => [
                'id' => $row['id'],
                'event' => $row['event'],
                'description_key' => $row['description_key'],
                'description' => $row['summary'],
                'occurred_at' => $row['happened_at']->toIso8601String(),
                'actor_name' => $row['actor'],
                'changes' => collect($row['lines'])->map(fn (array $line) => [
                    'label' => $line['label'],
                    'from' => $line['from'],
                    'to' => $line['to'],
                ])->all(),
            ])
            ->values()
            ->all();

        $setBy = CandidatePipelineStageActivity::currentStageSetter($candidate);

        return [
            'id' => $candidate->id,
            'person_id' => $candidate->person_id,
            'position_id' => $candidate->position_id,
            'pipeline_stage_id' => $candidate->pipeline_stage_id,
            'pipeline_stage' => $candidate->relationLoaded('pipelineStage') && $candidate->pipelineStage !== null
                ? [
                    'id' => $candidate->pipelineStage->id,
                    'key' => $candidate->pipelineStage->key,
                    'label' => $candidate->pipelineStage->label(),
                ]
                : null,
            'stage_set_by' => $setBy,
            'change_history' => $changeHistory,
            'source' => $candidate->source,
            'applied_at' => $candidate->applied_at?->toIso8601String(),
            'nationality' => $candidate->nationality,
            'driving_license_category' => $candidate->driving_license_category,
            'has_own_car' => $candidate->has_own_car,
            'german_level' => $candidate->german_level?->value,
            'available_from' => $candidate->available_from?->toDateString(),
            'housing_needed' => $candidate->housing_needed,
            'created_at' => $candidate->created_at?->toIso8601String(),
            'updated_at' => $candidate->updated_at?->toIso8601String(),
            'person' => $candidate->relationLoaded('person') && $candidate->person !== null
                ? [
                    'id' => $candidate->person->id,
                    'first_name' => $candidate->person->first_name,
                    'last_name' => $candidate->person->last_name,
                    'email' => $candidate->person->email,
                    'phone' => $candidate->person->phone,
                ]
                : null,
        ];
    }
}
