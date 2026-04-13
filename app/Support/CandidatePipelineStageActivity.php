<?php

namespace App\Support;

use App\Models\Candidate;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

final class CandidatePipelineStageActivity
{
    /**
     * @return Collection<int, Activity>
     */
    public static function historyForCandidate(Candidate $candidate): Collection
    {
        return $candidate->activities()
            ->with('causer')
            ->latest()
            ->get();
    }

    /**
     * User who last set the candidate's current pipeline stage (id + display name). Null if no matching log (e.g. legacy data).
     *
     * @return array{id: int|null, name: string}|null
     */
    public static function currentStageSetter(Candidate $candidate): ?array
    {
        $currentStageId = $candidate->pipeline_stage_id;

        $activity = $candidate->activities()
            ->with('causer')
            ->latest()
            ->get()
            ->first(function (Activity $activity) use ($currentStageId) {
                $to = self::toStageId($activity);

                return $to !== null && (int) $to === (int) $currentStageId;
            });

        if ($activity === null) {
            return null;
        }

        $causer = $activity->causer;

        if ($causer instanceof User) {
            return [
                'id' => (int) $causer->getKey(),
                'name' => (string) $causer->name,
            ];
        }

        return [
            'id' => null,
            'name' => __('common.system'),
        ];
    }

    public static function toStageId(Activity $activity): ?int
    {
        $changes = $activity->attribute_changes;
        if ($changes === null) {
            return null;
        }

        $attrs = $changes->get('attributes');

        if (! is_array($attrs) || ! array_key_exists('pipeline_stage_id', $attrs)) {
            return null;
        }

        $v = $attrs['pipeline_stage_id'];

        return $v === null ? null : (int) $v;
    }

    public static function fromStageId(Activity $activity): ?int
    {
        $changes = $activity->attribute_changes;
        if ($changes === null) {
            return null;
        }

        $old = $changes->get('old');
        if (! is_array($old) || ! array_key_exists('pipeline_stage_id', $old)) {
            return null;
        }

        $v = $old['pipeline_stage_id'];

        return $v === null ? null : (int) $v;
    }

    public static function stageLabel(?int $stageId): ?string
    {
        if ($stageId === null) {
            return null;
        }

        $stage = PipelineStage::query()->find($stageId);

        return $stage?->label();
    }
}
