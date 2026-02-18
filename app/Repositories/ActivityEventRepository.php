<?php

namespace App\Repositories;

use App\Models\ActivityEvent;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class ActivityEventRepository
{
    public function log(
        string $type,
        CarbonImmutable $occurredAt,
        ?string $subjectType = null,
        ?int $subjectId = null,
        ?array $meta = null,
        ?int $userId = null
    ): ActivityEvent {
        return ActivityEvent::query()->create([
            'type' => $type,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'occurred_at' => $occurredAt->toDateTimeString(),
            'meta' => $meta,
            'user_id' => $userId,
        ]);
    }

    public function countByTypeInPeriod(string $type, CarbonImmutable $from, CarbonImmutable $to): int
    {
        return ActivityEvent::query()
            ->where('type', $type)
            ->where('occurred_at', '>=', $from)
            ->where('occurred_at', '<=', $to)
            ->count();
    }

    /**
     * Get time-series aggregates for chart: count per period (day or week) grouped by type.
     *
     * @return array<int, array{period: string, type: string, count: int}>
     */
    public function getTimeSeriesAggregates(CarbonImmutable $from, CarbonImmutable $to, string $granularity = 'day'): array
    {
        $dateFormat = $granularity === 'week' ? '%Y-%u' : '%Y-%m-%d';
        $raw = ActivityEvent::query()
            ->where('occurred_at', '>=', $from)
            ->where('occurred_at', '<=', $to)
            ->selectRaw("date_trunc(?, occurred_at) as period, type, count(*) as count", [$granularity === 'week' ? 'week' : 'day'])
            ->groupBy('period', 'type')
            ->orderBy('period')
            ->orderBy('type')
            ->get();

        $out = [];
        foreach ($raw as $row) {
            $out[] = [
                'period' => $row->period instanceof \Carbon\Carbon
                    ? $row->period->format($granularity === 'week' ? 'Y-W' : 'Y-m-d')
                    : (string) $row->period,
                'type' => $row->type,
                'count' => (int) $row->count,
            ];
        }

        return $out;
    }

    /**
     * Simpler daily counts for chart (one row per day, types as columns).
     *
     * @return Collection<int, object{date: string, candidate_created: int, candidate_stage_changed: int, employee_hired: int, employee_terminated: int, meeting_scheduled: int, task_created: int}>
     */
    public function getDailyCountsForChart(CarbonImmutable $from, CarbonImmutable $to): Collection
    {
        $types = [
            ActivityEvent::TYPE_CANDIDATE_CREATED,
            ActivityEvent::TYPE_CANDIDATE_STAGE_CHANGED,
            ActivityEvent::TYPE_EMPLOYEE_HIRED,
            ActivityEvent::TYPE_EMPLOYEE_TERMINATED,
            ActivityEvent::TYPE_MEETING_SCHEDULED,
            ActivityEvent::TYPE_TASK_CREATED,
        ];

        $rows = ActivityEvent::query()
            ->where('occurred_at', '>=', $from)
            ->where('occurred_at', '<=', $to)
            ->selectRaw('(occurred_at::date) as date, type, count(*) as count')
            ->groupByRaw('(occurred_at::date), type')
            ->get();

        $byDate = [];
        $current = $from->toDateString();
        $end = $to->toDateString();
        while ($current <= $end) {
            $byDate[$current] = array_combine($types, array_fill(0, count($types), 0));
            $current = CarbonImmutable::parse($current)->addDay()->toDateString();
        }

        foreach ($rows as $row) {
            $d = $row->date instanceof \DateTimeInterface ? $row->date->format('Y-m-d') : $row->date;
            if (isset($byDate[$d]) && in_array($row->type, $types, true)) {
                $byDate[$d][$row->type] = (int) $row->count;
            }
        }

        $result = collect();
        foreach ($byDate as $date => $counts) {
            $result->push((object) array_merge(['date' => $date], $counts));
        }

        return $result;
    }
}
