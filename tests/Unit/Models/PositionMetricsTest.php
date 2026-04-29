<?php

use App\Models\Position;
use Carbon\CarbonImmutable;

test('position openForDays uses opens_at when present', function () {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-04-28'));

    $position = Position::factory()->create([
        'status' => 'open',
        'opens_at' => CarbonImmutable::parse('2026-04-20'),
        'closes_at' => null,
    ]);

    expect($position->openForDays())->toBe(8);

    CarbonImmutable::setTestNow();
});

test('position closesInDaysForDisplay clamps past close dates to zero', function () {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-04-28'));

    $position = Position::factory()->create([
        'status' => 'closed',
        'opens_at' => CarbonImmutable::parse('2026-04-01'),
        'closes_at' => CarbonImmutable::parse('2026-04-10'),
    ]);

    expect($position->closesInDays())->toBeLessThan(0)
        ->and($position->closesInDaysForDisplay())->toBe(0);

    CarbonImmutable::setTestNow();
});

