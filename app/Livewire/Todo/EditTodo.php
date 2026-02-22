<?php

namespace App\Livewire\Todo;

use App\Actions\Task\UpdateTaskAction;
use App\Data\Task\TaskData;
use App\Models\Task;
use Carbon\CarbonImmutable;
use Livewire\Component;

class EditTodo extends Component
{
    public Task $task;

    public string $title = '';

    public string $priority = 'medium';

    public string $dueDate = '';

    public bool $completed = false;

    public function mount(Task $task): void
    {
        $this->authorize('update', $task);
        $this->task = $task;
        $this->title = $task->title;
        $this->priority = $task->priority;
        $this->dueDate = $task->due_date->format('Y-m-d');
        $this->completed = $task->completed_at !== null;
    }

    public function save(): mixed
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'string', 'in:low,medium,high'],
            'dueDate' => ['required', 'date'],
            'completed' => ['boolean'],
        ], [], [
            'title' => __('task.title'),
            'priority' => __('task.priority'),
            'dueDate' => __('task.due_date'),
            'completed' => __('task.completed'),
        ]);

        $completedAt = $validated['completed']
            ? ($this->task->completed_at ? CarbonImmutable::parse($this->task->completed_at) : CarbonImmutable::now())
            : null;

        $data = new TaskData(
            userId: $this->task->user_id,
            title: $validated['title'],
            priority: $validated['priority'],
            dueDate: CarbonImmutable::parse($validated['dueDate']),
            completedAt: $completedAt,
        );

        app(UpdateTaskAction::class)->handle($this->task, $data);

        $this->dispatch('notify', __('task.updated'));

        return $this->redirect(route('todo.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.todo.edit-todo')->title(__('task.edit'));
    }
}
