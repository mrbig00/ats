<?php

namespace App\Livewire\Todo;

use App\Actions\Task\CreateTaskAction;
use App\Data\Task\TaskData;
use Carbon\CarbonImmutable;
use Livewire\Component;

class CreateTodo extends Component
{
    public string $title = '';

    public string $priority = 'medium';

    public string $dueDate = '';

    public function mount(): void
    {
        $this->authorize('create', \App\Models\Task::class);
        if ($this->dueDate === '') {
            $this->dueDate = CarbonImmutable::today()->addDay()->format('Y-m-d');
        }
    }

    public function save(): mixed
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'string', 'in:low,medium,high'],
            'dueDate' => ['required', 'date'],
        ], [], [
            'title' => __('task.title'),
            'priority' => __('task.priority'),
            'dueDate' => __('task.due_date'),
        ]);

        $data = new TaskData(
            userId: auth()->id(),
            title: $validated['title'],
            priority: $validated['priority'],
            dueDate: CarbonImmutable::parse($validated['dueDate']),
            completedAt: null,
        );

        app(CreateTaskAction::class)->handle($data);

        $this->dispatch('notify', __('task.created'));

        return $this->redirect(route('todo.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.todo.create-todo')->title(__('task.create'));
    }
}
