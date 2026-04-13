<?php

use App\Actions\Positions\ReopenPositionAction;
use App\Models\Position;
use Carbon\CarbonImmutable;

test('reopen position action opens a closed position', function () {
    $position = Position::factory()->create([
        'status' => 'closed',
        'closes_at' => null,
    ]);

    $result = app(ReopenPositionAction::class)->handle($position);

    expect($result->status)->toBe('open');
});

test('reopen position action clears past closes at', function () {
    $position = Position::factory()->create([
        'status' => 'open',
        'closes_at' => CarbonImmutable::today()->subDays(5),
    ]);

    expect($position->hasExpiredRecruitmentSession())->toBeTrue();

    $result = app(ReopenPositionAction::class)->handle($position);

    expect($result->status)->toBe('open')
        ->and($result->closes_at)->toBeNull();
});

test('reopen position action throws when posting is still active', function () {
    $position = Position::factory()->create([
        'status' => 'open',
        'closes_at' => null,
    ]);

    app(ReopenPositionAction::class)->handle($position);
})->throws(InvalidArgumentException::class);

test('reopen position action throws when closes at is still in the future', function () {
    $position = Position::factory()->create([
        'status' => 'open',
        'closes_at' => CarbonImmutable::today()->addMonth(),
    ]);

    app(ReopenPositionAction::class)->handle($position);
})->throws(InvalidArgumentException::class);
