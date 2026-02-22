<?php

use App\Actions\Task\UpdateTaskAction;
use App\Data\Task\TaskData;
use App\Models\Task;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('update task action updates task', function () {
    $user = User::factory()->create();
    $task = Task::query()->create([
        'user_id' => $user->id,
        'title' => 'Old title',
        'priority' => 'low',
        'due_date' => '2025-02-15',
        'completed_at' => null,
    ]);

    $data = new TaskData(
        userId: $user->id,
        title: 'Updated title',
        priority: 'high',
        dueDate: CarbonImmutable::parse('2025-03-10'),
        completedAt: CarbonImmutable::now(),
    );

    $action = app(UpdateTaskAction::class);
    $updated = $action->handle($task, $data);

    expect($updated->title)->toBe('Updated title')
        ->and($updated->priority)->toBe('high')
        ->and($updated->due_date->format('Y-m-d'))->toBe('2025-03-10')
        ->and($updated->completed_at)->not->toBeNull();
});
