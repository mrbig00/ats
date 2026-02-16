<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <flux:button variant="ghost" icon="arrow-left" :href="route('jobs.index')" wire:navigate>
                {{ __('common.back') }}
            </flux:button>
            <flux:heading size="xl" level="1">{{ $position->title }}</flux:heading>
            <flux:badge size="lg" :color="$position->isOpen() ? 'green' : 'zinc'" inset="top bottom">
                {{ $position->statusLabel() }}
            </flux:badge>
        </div>
        <div class="flex flex-wrap gap-2">
            @can('edit', $position)
                <flux:button icon="pencil" :href="route('jobs.edit', $position)" wire:navigate variant="primary">
                    {{ __('common.edit') }}
                </flux:button>
            @endcan
            @can('delete', $position)
                @if ($position->isOpen())
                    <flux:button
                        icon="trash"
                        variant="danger"
                        wire:click="deletePosition"
                        wire:confirm="{{ __('job.confirm_delete') }}"
                        wire:loading.attr="disabled"
                    >
                        {{ __('common.delete') }}
                    </flux:button>
                @endif
            @endcan
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('job.details') }}</flux:heading>
                @if ($position->description)
                    <flux:text class="whitespace-pre-wrap">{{ $position->description }}</flux:text>
                @else
                    <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('job.no_description') }}</flux:text>
                @endif
                <dl class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div>
                        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('job.opens_at') }}</flux:text>
                        <flux:text>{{ $position->opens_at?->isoFormat('L') ?? '—' }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('job.closes_at') }}</flux:text>
                        <flux:text>{{ $position->closes_at?->isoFormat('L') ?? '—' }}</flux:text>
                    </div>
                </dl>
            </flux:card>
        </div>
        <div class="space-y-6">
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('job.candidates_count') }}</flux:heading>
                <flux:text class="text-2xl font-semibold">{{ $position->candidates_count }}</flux:text>
                <flux:button size="sm" class="mt-2" :href="route('candidates.index', ['position' => $position->id])" wire:navigate variant="outline">
                    {{ __('job.view_candidates') }}
                </flux:button>
            </flux:card>
        </div>
    </div>
</div>
