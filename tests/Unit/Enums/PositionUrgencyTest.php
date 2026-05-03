<?php

use App\Enums\PositionUrgency;

it('maps common csv synonyms to canonical backing values', function () {
    expect(PositionUrgency::normalizeCsvValue('high'))->toBe('urgent');
    expect(PositionUrgency::normalizeCsvValue('HIGH'))->toBe('urgent');
    expect(PositionUrgency::normalizeCsvValue('critical'))->toBe('urgent');
    expect(PositionUrgency::normalizeCsvValue('normal'))->toBe('medium');
    expect(PositionUrgency::normalizeCsvValue('moderate'))->toBe('medium');
    expect(PositionUrgency::normalizeCsvValue('low'))->toBe('good');
});

it('passes through canonical enum values', function () {
    expect(PositionUrgency::normalizeCsvValue('urgent'))->toBe('urgent');
    expect(PositionUrgency::normalizeCsvValue('medium'))->toBe('medium');
    expect(PositionUrgency::normalizeCsvValue('good'))->toBe('good');
});

it('returns null for blank strings', function () {
    expect(PositionUrgency::normalizeCsvValue(''))->toBeNull();
    expect(PositionUrgency::normalizeCsvValue('   '))->toBeNull();
});

it('returns unknown tokens unchanged for validation to reject', function () {
    expect(PositionUrgency::normalizeCsvValue('nope'))->toBe('nope');
});
