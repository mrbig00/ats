<?php

use App\Models\User;
use Illuminate\Auth\Events\Login;

test('guest uses session locale when supported', function () {
    $response = $this->withSession(['locale' => 'ro'])->get('/login');

    $response->assertOk();
    expect(app()->getLocale())->toBe('ro');
});

test('guest uses accept language when supported', function () {
    $response = $this->withHeader('Accept-Language', 'ro,en;q=0.9')->get('/login');

    $response->assertOk();
    expect(app()->getLocale())->toBe('ro');
    expect(session('locale'))->toBe('ro');
});

test('guest falls back to en when no session or supported accept language', function () {
    $response = $this->withHeader('Accept-Language', 'fr,ja;q=0.9')->get('/login');

    $response->assertOk();
    expect(app()->getLocale())->toBe('en');
});

test('authenticated user uses stored language', function () {
    $user = User::factory()->create(['language' => 'hu']);

    $response = $this->actingAs($user)->get('/settings/profile');

    $response->assertOk();
    expect(app()->getLocale())->toBe('hu');
    expect(session('locale'))->toBe('hu');
});

test('authenticated user falls back to app locale when language missing', function () {
    $user = User::factory()->create(['language' => null]);

    $response = $this->actingAs($user)->get('/settings/profile');

    $response->assertOk();
    expect(app()->getLocale())->toBe(config('app.locale'));
});

test('login listener syncs user language to session', function () {
    $user = User::factory()->create(['language' => 'de']);

    event(new Login('web', $user, false));

    expect(session('locale'))->toBe('de');
});
