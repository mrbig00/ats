<?php

use App\Models\ActivityEvent;
use App\Repositories\ActivityEventRepository;
use Carbon\CarbonImmutable;

beforeEach(function () {
    $this->repository = app(ActivityEventRepository::class);
});

test('logs activity event', function () {
    $occurredAt = CarbonImmutable::now();

    $event = $this->repository->log(
        ActivityEvent::TYPE_CANDIDATE_CREATED,
        $occurredAt,
        'candidate',
        1,
        ['position_id' => 2],
        null
    );

    expect($event)->toBeInstanceOf(ActivityEvent::class)
        ->and($event->type)->toBe(ActivityEvent::TYPE_CANDIDATE_CREATED)
        ->and($event->subject_type)->toBe('candidate')
        ->and($event->subject_id)->toBe(1)
        ->and($event->meta)->toBe(['position_id' => 2]);
});

test('counts by type in period', function () {
    $from = CarbonImmutable::today()->subDays(5);
    $to = CarbonImmutable::today();

    $this->repository->log(ActivityEvent::TYPE_CANDIDATE_CREATED, $from->addHours(1), null, null, null, null);
    $this->repository->log(ActivityEvent::TYPE_CANDIDATE_CREATED, $to->subHours(1), null, null, null, null);
    $this->repository->log(ActivityEvent::TYPE_MEETING_SCHEDULED, $from->addHours(2), null, null, null, null);

    $count = $this->repository->countByTypeInPeriod(ActivityEvent::TYPE_CANDIDATE_CREATED, $from, $to);

    expect($count)->toBe(2);
});
