<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <flux:button variant="ghost" icon="arrow-left" :href="route('meetings.index')" wire:navigate>
                {{ __('common.back') }}
            </flux:button>
            <flux:heading size="xl" level="1">{{ $event->title }}</flux:heading>
            <flux:badge size="sm" :color="$event->isInterview() ? 'green' : 'blue'" inset="top bottom">
                {{ $event->isInterview() ? __('meeting.type_interview') : __('meeting.type_internal') }}
            </flux:badge>
        </div>
        <div class="flex gap-2">
            @can('update', $event)
                <flux:button icon="pencil" :href="route('meetings.edit', $event)" wire:navigate variant="outline">
                    {{ __('common.edit') }}
                </flux:button>
            @endcan
            @can('delete', $event)
                <flux:button icon="trash" variant="danger" wire:click="$set('showDeleteConfirm', true)">
                    {{ __('common.delete') }}
                </flux:button>
            @endcan
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('meeting.details') }}</flux:heading>
                <dl class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('meeting.starts_at') }}</flux:text>
                        <flux:text>{{ $event->starts_at->isoFormat('L LT') }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('meeting.ends_at') }}</flux:text>
                        <flux:text>{{ $event->ends_at?->isoFormat('L LT') ?? '—' }}</flux:text>
                    </div>
                    @if ($event->candidate)
                        <div class="sm:col-span-2">
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('meeting.candidate') }}</flux:text>
                            <flux:link :href="route('candidates.show', $event->candidate)" wire:navigate class="font-medium">
                                {{ $event->candidate->person->fullName() }}
                            </flux:link>
                        </div>
                    @endif
                </dl>
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('meeting.notes') }}</flux:heading>
                @if ($event->notes)
                    <flux:text class="whitespace-pre-wrap">{{ $event->notes }}</flux:text>
                @else
                    <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('meeting.no_notes') }}</flux:text>
                @endif
            </flux:card>
        </div>
    </div>

    <flux:modal wire:model="showDeleteConfirm" class="max-w-md">
        <flux:heading size="lg">{{ __('meeting.confirm_delete') }}</flux:heading>
        <flux:text class="mt-2">{{ __('meeting.confirm_delete_text') }}</flux:text>
        <div class="mt-4 flex gap-2 justify-end">
            <flux:button variant="ghost" wire:click="$set('showDeleteConfirm', false)">{{ __('common.cancel') }}</flux:button>
            <flux:button variant="danger" wire:click="deleteMeeting" wire:loading.attr="disabled">{{ __('common.delete') }}</flux:button>
        </div>
    </flux:modal>
</div>
