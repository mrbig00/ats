<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">{{ __('nav.todo') }}</flux:heading>
        @can('create', \App\Models\Task::class)
            <flux:button icon="plus" :href="route('todo.create')" wire:navigate variant="primary">
                {{ __('task.create') }}
            </flux:button>
        @endcan
    </div>

    <div wire:loading.class="opacity-50 pointer-events-none" class="relative">
        <flux:table :paginate="$tasks">
            <thead data-flux-columns>
                <tr>
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'title'"
                        :direction="$sortDirection"
                        wire:click="sortBy('title')"
                    >
                        {{ __('task.title') }}
                    </flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'priority'"
                        :direction="$sortDirection"
                        wire:click="sortBy('priority')"
                    >
                        {{ __('task.priority') }}
                    </flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'due_date'"
                        :direction="$sortDirection"
                        wire:click="sortBy('due_date')"
                    >
                        {{ __('task.due_date') }}
                    </flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'completed_at'"
                        :direction="$sortDirection"
                        wire:click="sortBy('completed_at')"
                    >
                        {{ __('task.completed') }}
                    </flux:table.column>
                    <flux:table.column></flux:table.column>
                </tr>
                <tr>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30">
                        <div class="flex flex-col gap-1">
                            <flux:input
                                wire:model.live.debounce.300ms="search"
                                type="search"
                                :placeholder="__('common.search')"
                                class="min-w-full max-w-[200px]"
                            />
                        </div>
                    </th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30">
                        <div class="flex flex-col gap-1">
                            <flux:select wire:model.live="priorityFilter" :placeholder="__('task.filter_priority')" class="min-w-full max-w-[140px]">
                                <flux:select.option value="">{{ __('task.all_priorities') }}</flux:select.option>
                                <flux:select.option value="high">{{ __('task.priority_high') }}</flux:select.option>
                                <flux:select.option value="medium">{{ __('task.priority_medium') }}</flux:select.option>
                                <flux:select.option value="low">{{ __('task.priority_low') }}</flux:select.option>
                            </flux:select>
                        </div>
                    </th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30">
                        <div class="flex flex-col gap-1">
                            <flux:input
                                wire:model.live="dueDateFrom"
                                type="date"
                                :placeholder="__('task.filter_due_from')"
                                class="min-w-full max-w-[160px]"
                            />
                            <flux:input
                                wire:model.live="dueDateTo"
                                type="date"
                                :placeholder="__('task.filter_due_to')"
                                class="min-w-full max-w-[160px] mt-1"
                            />
                        </div>
                    </th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30">
                        <div class="flex flex-col gap-1">
                            <flux:select wire:model.live="completedFilter" :placeholder="__('task.filter_completed')" class="min-w-full max-w-[140px]">
                                <flux:select.option value="">{{ __('task.all') }}</flux:select.option>
                                <flux:select.option value="0">{{ __('task.incomplete') }}</flux:select.option>
                                <flux:select.option value="1">{{ __('task.complete') }}</flux:select.option>
                            </flux:select>
                        </div>
                    </th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                </tr>
            </thead>
            <flux:table.rows>
                @forelse ($tasks as $task)
                    <flux:table.row :key="$task->id">
                        <flux:table.cell>
                            <flux:link :href="route('todo.edit', $task)" wire:navigate class="font-medium {{ $task->isCompleted() ? 'line-through text-zinc-500 dark:text-zinc-400' : '' }}">
                                {{ $task->title }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" :color="$task->priority === 'high' ? 'red' : ($task->priority === 'medium' ? 'yellow' : 'zinc')" inset="top bottom">
                                {{ __('task.priority_' . $task->priority) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">
                            {{ $task->due_date->isoFormat('L') }}
                            @if ($task->isOverdue())
                                <flux:badge size="sm" color="red" class="ms-1">{{ __('task.overdue') }}</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($task->completed_at)
                                {{ $task->completed_at->isoFormat('L') }}
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="sm" icon="pencil" :href="route('todo.edit', $task)" wire:navigate variant="ghost" inset="top bottom">
                                {{ __('common.edit') }}
                            </flux:button>
                            @can('delete', $task)
                                <flux:button size="sm" icon="trash" wire:click="deleteTask({{ $task->id }})" wire:confirm="{{ __('task.confirm_delete') }}" variant="ghost" inset="top bottom" class="text-red-600 dark:text-red-400">
                                    {{ __('common.delete') }}
                                </flux:button>
                            @endcan
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="py-8 text-center">
                            <flux:callout variant="secondary" icon="clipboard-document-check" class="mx-auto max-w-md">
                                <flux:callout.heading>{{ __('task.no_tasks') }}</flux:callout.heading>
                                <flux:callout.text>{{ __('task.no_tasks_hint') }}</flux:callout.text>
                                @can('create', \App\Models\Task::class)
                                    <x-slot name="actions">
                                        <flux:button size="sm" icon="plus" :href="route('todo.create')" wire:navigate variant="primary">
                                            {{ __('task.create') }}
                                        </flux:button>
                                    </x-slot>
                                @endcan
                            </flux:callout>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
