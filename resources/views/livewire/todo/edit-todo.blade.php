<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center gap-2">
        <flux:button variant="ghost" icon="arrow-left" :href="route('todo.index')" wire:navigate>
            {{ __('common.back') }}
        </flux:button>
        <flux:heading size="xl" level="1">{{ __('task.edit') }}</flux:heading>
    </div>

    <div class="flex flex-1 items-center justify-center">
        <flux:card class="w-full max-w-2xl">
            <form wire:submit="save" class="space-y-6">
                <flux:field>
                    <flux:label>{{ __('task.title') }}</flux:label>
                    <flux:input wire:model="title" type="text" required />
                    <flux:error name="title" />
                </flux:field>
                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('task.priority') }}</flux:label>
                        <flux:select wire:model="priority" class="w-full">
                            <flux:select.option value="high">{{ __('task.priority_high') }}</flux:select.option>
                            <flux:select.option value="medium">{{ __('task.priority_medium') }}</flux:select.option>
                            <flux:select.option value="low">{{ __('task.priority_low') }}</flux:select.option>
                        </flux:select>
                        <flux:error name="priority" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('task.due_date') }}</flux:label>
                        <flux:input wire:model="dueDate" type="date" required />
                        <flux:error name="dueDate" />
                    </flux:field>
                </div>
                <flux:field>
                    <flux:checkbox wire:model="completed" :label="__('task.mark_completed')" />
                    <flux:error name="completed" />
                </flux:field>
                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        {{ __('common.save') }}
                    </flux:button>
                    <flux:button type="button" variant="ghost" :href="route('todo.index')" wire:navigate>
                        {{ __('common.cancel') }}
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</div>
