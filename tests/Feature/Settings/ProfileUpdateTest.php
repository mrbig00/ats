<?php

use App\Livewire\Settings\Profile;
use App\Models\User;
use Livewire\Livewire;

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/settings/profile')->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(Profile::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('language', 'en')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(Profile::class)
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->set('language', 'en')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect($user->fresh())->toBeNull();
    expect(auth()->check())->toBeFalse();
});

test('profile language can be updated', function () {
    $user = User::factory()->create(['language' => 'en']);

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('name', $user->name)
        ->set('email', $user->email)
        ->set('language', 'ro')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->language)->toBe('ro');
    expect(session('locale'))->toBe('ro');
    expect(app()->getLocale())->toBe('ro');
});

test('profile rejects unsupported language', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('name', $user->name)
        ->set('email', $user->email)
        ->set('language', 'fr')
        ->call('updateProfileInformation')
        ->assertHasErrors(['language']);
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
});
