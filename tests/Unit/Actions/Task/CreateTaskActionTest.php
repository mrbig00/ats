<?php

use App\Actions\Task\CreateTaskAction;
use App\Data\Task\TaskData;
use App\Events\TaskCreated;
use App\Models\Task;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

test('create task action creates task and dispatches TaskCreated event', function () {
    Event::fake([TaskCreated::class]);

    $user = User::factory()->create();
    $data = new TaskData(
        userId: $user->id,
        title: 'Review PR',
        priority: 'high',
        dueDate: CarbonImmutable::parse('2025-03-01'),
        completedAt: null,
    );

    $action = app(CreateTaskAction::class);
    $task = $action->handle($data);

    expect($task)->toBeInstanceOf(Task::class)
        ->and($task->user_id)->toBe($user->id)
        ->and($task->title)->toBe('Review PR')
        ->and($task->priority)->toBe('high')
        ->and($task->due_date->format('Y-m-d'))->toBe('2025-03-01')
        ->and($task->completed_at)->toBeNull()
        ->and(Task::count())->toBe(1);

    Event::assertDispatched(TaskCreated::class, fn (TaskCreated $e) => $e->taskId === $task->id && $e->userId === $user->id);
});
