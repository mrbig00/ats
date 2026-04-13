<?php

namespace App\Support;

use App\Enums\GermanLanguageLevel;
use App\Models\Candidate;
use App\Models\Person;
use App\Models\PipelineStage;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

final class CandidateActivityPresentation
{
    /**
     * @return list<array{id: int, event: string|null, description_key: string, happened_at: \Carbon\CarbonInterface, summary: string, actor: string, lines: list<array{label: string, from: string, to: string}>}>
     */
    public static function changeHistoryRows(Candidate $candidate): array
    {
        if (! $candidate->relationLoaded('activities')) {
            $candidate->load(['activities' => fn ($q) => $q->with('causer')->latest()]);
        }

        $activities = $candidate->activities->sortBy('created_at')->values();
        [$positions, $stages, $persons] = self::prefetchRelatedMaps($activities);

        return $activities->map(function (Activity $activity) use ($positions, $stages, $persons) {
            return [
                'id' => (int) $activity->id,
                'event' => $activity->event,
                'description_key' => $activity->description,
                'happened_at' => $activity->created_at,
                'summary' => __($activity->description),
                'actor' => $activity->causer instanceof User
                    ? $activity->causer->name
                    : __('common.system'),
                'lines' => self::linesForActivity($activity, $positions, $stages, $persons),
            ];
        })->all();
    }

    /**
     * @param  Collection<int, Activity>  $activities
     * @return array{0: Collection<int, Position>, 1: Collection<int, PipelineStage>, 2: Collection<int, Person>}
     */
    private static function prefetchRelatedMaps(Collection $activities): array
    {
        $positionIds = [];
        $stageIds = [];
        $personIds = [];
        foreach ($activities as $activity) {
            $ch = $activity->attribute_changes;
            if ($ch === null) {
                continue;
            }
            foreach (['attributes', 'old'] as $side) {
                $arr = $ch->get($side);
                if (! is_array($arr)) {
                    continue;
                }
                if (array_key_exists('position_id', $arr) && $arr['position_id'] !== null) {
                    $positionIds[] = (int) $arr['position_id'];
                }
                if (array_key_exists('pipeline_stage_id', $arr) && $arr['pipeline_stage_id'] !== null) {
                    $stageIds[] = (int) $arr['pipeline_stage_id'];
                }
                if (array_key_exists('person_id', $arr) && $arr['person_id'] !== null) {
                    $personIds[] = (int) $arr['person_id'];
                }
            }
        }

        $positions = Position::query()->whereIn('id', array_unique($positionIds))->get()->keyBy('id');
        $stages = PipelineStage::query()->whereIn('id', array_unique($stageIds))->get()->keyBy('id');
        $persons = Person::query()->whereIn('id', array_unique($personIds))->get()->keyBy('id');

        return [$positions, $stages, $persons];
    }

    /**
     * @param  Collection<int, Position>  $positions
     * @param  Collection<int, PipelineStage>  $stages
     * @param  Collection<int, Person>  $persons
     * @return list<array{label: string, from: string, to: string}>
     */
    private static function linesForActivity(Activity $activity, Collection $positions, Collection $stages, Collection $persons): array
    {
        $ch = $activity->attribute_changes;
        if ($ch === null) {
            return [];
        }

        $attrs = $ch->get('attributes');
        $olds = $ch->get('old');
        if (! is_array($attrs)) {
            return [];
        }

        if (! is_array($olds)) {
            $olds = [];
        }

        $event = $activity->event;
        $lines = [];

        foreach ($attrs as $key => $newRaw) {
            $keyStr = (string) $key;
            $label = self::attributeLabel($keyStr);
            $oldRaw = array_key_exists($key, $olds) ? $olds[$key] : null;

            if ($event === 'created') {
                $lines[] = [
                    'label' => $label,
                    'from' => __('common.em_dash'),
                    'to' => self::formatValue($keyStr, $newRaw, $positions, $stages, $persons),
                ];
            } else {
                $lines[] = [
                    'label' => $label,
                    'from' => self::formatValue($keyStr, $oldRaw, $positions, $stages, $persons),
                    'to' => self::formatValue($keyStr, $newRaw, $positions, $stages, $persons),
                ];
            }
        }

        return $lines;
    }

    private static function attributeLabel(string $key): string
    {
        return match ($key) {
            'person_id' => __('candidate.person_link'),
            'position_id' => __('candidate.position'),
            'pipeline_stage_id' => __('candidate.stage'),
            'source' => __('candidate.source'),
            'applied_at' => __('candidate.applied_at'),
            'nationality' => __('candidate.nationality'),
            'driving_license_category' => __('candidate.driving_license_category'),
            'has_own_car' => __('candidate.has_own_car'),
            'german_level' => __('candidate.german_level'),
            'available_from' => __('candidate.available_from'),
            'housing_needed' => __('candidate.housing_needed'),
            default => $key,
        };
    }

    /**
     * @param  Collection<int, Position>  $positions
     * @param  Collection<int, PipelineStage>  $stages
     * @param  Collection<int, Person>  $persons
     */
    private static function formatValue(string $key, mixed $raw, Collection $positions, Collection $stages, Collection $persons): string
    {
        if ($raw === null || $raw === '') {
            return __('common.em_dash');
        }

        return match ($key) {
            'pipeline_stage_id' => $stages->get((int) $raw)?->label() ?? (string) $raw,
            'position_id' => $positions->get((int) $raw)?->title ?? (string) $raw,
            'person_id' => ($p = $persons->get((int) $raw)) !== null
                ? $p->fullName()
                : (string) $raw,
            'has_own_car', 'housing_needed' => self::formatBoolish($raw),
            'german_level' => self::formatGermanLevel($raw),
            'applied_at' => self::formatDateTimeString((string) $raw),
            'available_from' => self::formatDateString((string) $raw),
            default => is_scalar($raw) ? (string) $raw : __('common.em_dash'),
        };
    }

    private static function formatBoolish(mixed $raw): string
    {
        if ($raw === true || $raw === 1 || $raw === '1') {
            return __('common.yes');
        }
        if ($raw === false || $raw === 0 || $raw === '0') {
            return __('common.no');
        }

        return __('common.not_specified');
    }

    private static function formatGermanLevel(mixed $raw): string
    {
        if ($raw === null || $raw === '') {
            return __('common.em_dash');
        }
        try {
            return GermanLanguageLevel::from((string) $raw)->label();
        } catch (\ValueError) {
            return (string) $raw;
        }
    }

    private static function formatDateTimeString(string $raw): string
    {
        try {
            return Carbon::parse($raw)->isoFormat('L LT');
        } catch (\Throwable) {
            return $raw;
        }
    }

    private static function formatDateString(string $raw): string
    {
        try {
            return Carbon::parse($raw)->isoFormat('L');
        } catch (\Throwable) {
            return $raw;
        }
    }
}
