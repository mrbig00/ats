<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center gap-2">
        <flux:button variant="ghost" icon="arrow-left" :href="route('meetings.show', $event)" wire:navigate>
            {{ __('common.back') }}
        </flux:button>
        <flux:heading size="xl" level="1">{{ __('meeting.edit') }}</flux:heading>
    </div>

    <div class="flex flex-1 items-center justify-center">
        <flux:card class="w-full max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <flux:field>
                <flux:label>{{ __('meeting.title') }}</flux:label>
                <flux:input wire:model="title" type="text" required />
                <flux:error name="title" />
            </flux:field>
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('meeting.starts_at') }}</flux:label>
                    <flux:input wire:model="startsAt" type="datetime-local" required />
                    <flux:error name="startsAt" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('meeting.ends_at') }}</flux:label>
                    <flux:input wire:model="endsAt" type="datetime-local" />
                    <flux:error name="endsAt" />
                </flux:field>
            </div>
            <flux:field>
                <flux:label>{{ __('meeting.notes') }}</flux:label>
                <flux:textarea wire:model="notes" rows="4" />
                <flux:error name="notes" />
            </flux:field>
            <div class="flex gap-2">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    {{ __('common.save') }}
                </flux:button>
                <flux:button type="button" variant="ghost" :href="route('meetings.show', $event)" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
    </div>
</div>
