<?php

test('application boots with opentelemetry sdk disabled', function () {
    expect(config('opentelemetry.disabled'))->toBeTrue();
});

test('opentelemetry service name is configured', function () {
    expect(config('opentelemetry.service_name'))->toBe('ats');
});
