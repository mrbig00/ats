<?php

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

test('meetings index requires authentication', function (): void {
    $this->getJson('/api/v1/meetings')
        ->assertUnauthorized();
});

test('meetings index returns paginated meetings for authenticated user', function (): void {
    Sanctum::actingAs($this->user, ['*']);

    CalendarEvent::factory()->count(2)->create([
        'type' => CalendarEvent::TYPE_INTERNAL_MEETING,
    ]);

    $response = $this->getJson('/api/v1/meetings');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                ['id', 'type', 'title', 'notes', 'starts_at', 'ends_at', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ])
        ->assertJsonCount(2, 'data');
});

test('meetings show requires authentication', function (): void {
    $event = CalendarEvent::factory()->create([
        'type' => CalendarEvent::TYPE_INTERNAL_MEETING,
    ]);

    $this->getJson("/api/v1/meetings/{$event->id}")
        ->assertUnauthorized();
});

test('meetings show returns meeting when authenticated', function (): void {
    Sanctum::actingAs($this->user, ['*']);

    $event = CalendarEvent::factory()->create([
        'type' => CalendarEvent::TYPE_INTERNAL_MEETING,
        'title' => 'API Test Meeting',
    ]);

    $response = $this->getJson("/api/v1/meetings/{$event->id}");

    $response->assertSuccessful()
        ->assertJsonPath('data.id', $event->id)
        ->assertJsonPath('data.title', 'API Test Meeting')
        ->assertJsonPath('data.type', CalendarEvent::TYPE_INTERNAL_MEETING);
});

test('meetings show returns 404 for non-meeting calendar event type', function (): void {
    Sanctum::actingAs($this->user, ['*']);

    $event = CalendarEvent::factory()->create([
        'type' => CalendarEvent::TYPE_ENTRY_DATE,
    ]);

    $this->getJson("/api/v1/meetings/{$event->id}")
        ->assertNotFound();
});

test('meetings store requires authentication', function (): void {
    $this->postJson('/api/v1/meetings', [
        'title' => 'New Meeting',
        'starts_at' => now()->addDay()->toIso8601String(),
        'ends_at' => now()->addDay()->addHour()->toIso8601String(),
    ])->assertUnauthorized();
});

test('meetings store creates meeting when authenticated', function (): void {
    Sanctum::actingAs($this->user, ['*']);

    $startsAt = now()->addDay()->startOfHour();
    $endsAt = $startsAt->copy()->addHour();

    $response = $this->postJson('/api/v1/meetings', [
        'title' => 'New API Meeting',
        'starts_at' => $startsAt->toIso8601String(),
        'ends_at' => $endsAt->toIso8601String(),
        'notes' => 'Notes here',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'New API Meeting')
        ->assertJsonPath('data.notes', 'Notes here')
        ->assertJsonPath('data.type', CalendarEvent::TYPE_INTERNAL_MEETING);

    $this->assertDatabaseHas('calendar_events', [
        'title' => 'New API Meeting',
        'type' => CalendarEvent::TYPE_INTERNAL_MEETING,
    ]);
});

test('meetings store validates required fields', function (): void {
    Sanctum::actingAs($this->user, ['*']);

    $this->postJson('/api/v1/meetings', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'starts_at']);
});

test('meetings update requires authentication', function (): void {
    $event = CalendarEvent::factory()->create([
        'type' => CalendarEvent::TYPE_INTERNAL_MEETING,
    ]);

    $this->putJson("/api/v1/meetings/{$event->id}", [
        'title' => 'Updated',
        'starts_at' => $event->starts_at->toIso8601String(),
        'ends_at' => $event->ends_at?->toIso8601String(),
    ])->assertUnauthorized();
});

test('meetings update modifies meeting when authenticated', function (): void {
    Sanctum::actingAs($this->user, ['*']);

    $event = CalendarEvent::factory()->create([
        'type' => CalendarEvent::TYPE_INTERNAL_MEETING,
        'title' => 'Old Title',
    ]);

    $startsAt = now()->addDays(2)->startOfHour();
    $endsAt = $startsAt->copy()->addHour();

    $response = $this->putJson("/api/v1/meetings/{$event->id}", [
        'title' => 'Updated Title',
        'starts_at' => $startsAt->toIso8601String(),
        'ends_at' => $endsAt->toIso8601String(),
        'notes' => 'Updated notes',
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.title', 'Updated Title')
        ->assertJsonPath('data.notes', 'Updated notes');

    $event->refresh();
    expect($event->title)->toBe('Updated Title')
        ->and($event->notes)->toBe('Updated notes');
});

test('meetings destroy requires authentication', function (): void {
    $event = CalendarEvent::factory()->create([
        'type' => CalendarEvent::TYPE_INTERNAL_MEETING,
    ]);

    $this->deleteJson("/api/v1/meetings/{$event->id}")
        ->assertUnauthorized();
});

test('meetings destroy deletes meeting when authenticated', function (): void {
    Sanctum::actingAs($this->user, ['*']);

    $event = CalendarEvent::factory()->create([
        'type' => CalendarEvent::TYPE_INTERNAL_MEETING,
    ]);

    $response = $this->deleteJson("/api/v1/meetings/{$event->id}");

    $response->assertNoContent();
    $this->assertDatabaseMissing('calendar_events', ['id' => $event->id]);
});

test('token endpoint returns bearer token for valid credentials', function (): void {
    $user = User::factory()->create([
        'email' => 'api@example.com',
    ]);

    $response = $this->postJson('/api/v1/tokens', [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'Test Device',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure(['token']);

    $token = $response->json('token');
    $this->assertNotEmpty($token);

    $this->getJson('/api/v1/meetings', [
        'Authorization' => 'Bearer '.$token,
    ])->assertSuccessful();
});

test('token endpoint returns 422 for invalid credentials', function (): void {
    User::factory()->create(['email' => 'api@example.com']);

    $this->postJson('/api/v1/tokens', [
        'email' => 'api@example.com',
        'password' => 'wrong',
        'device_name' => 'Test',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});
