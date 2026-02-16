<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <flux:button variant="ghost" icon="arrow-left" :href="route('meetings.index')" wire:navigate>
                {{ __('common.back') }}
            </flux:button>
            <flux:heading size="xl" level="1">{{ __('meeting.view_calendar') }}</flux:heading>
        </div>
        <div class="flex items-center gap-2">
            <flux:button variant="outline" size="sm" wire:click="previousMonth" wire:loading.attr="disabled">
                ← {{ __('meeting.prev_month') }}
            </flux:button>
            <flux:text class="font-medium min-w-[180px] text-center">{{ $monthLabel }}</flux:text>
            <flux:button variant="outline" size="sm" wire:click="nextMonth" wire:loading.attr="disabled">
                {{ __('meeting.next_month') }} →
            </flux:button>
            @can('create', \App\Models\CalendarEvent::class)
                <flux:button icon="plus" :href="route('meetings.create')" wire:navigate variant="primary">
                    {{ __('meeting.create') }}
                </flux:button>
            @endcan
        </div>
    </div>

    <flux:card>
        <div class="space-y-6">
            @foreach ($eventsByDate as $date => $dayEvents)
                <div>
                    <flux:heading size="md" class="mb-2 text-zinc-500 dark:text-zinc-400">
                        {{ \Carbon\CarbonImmutable::parse($date)->isoFormat('dddd, LL') }}
                    </flux:heading>
                    <div class="space-y-2">
                        @foreach ($dayEvents as $event)
                            <a
                                href="{{ route('meetings.show', $event) }}"
                                wire:navigate
                                class="flex items-center gap-4 rounded-lg border border-zinc-200 p-3 transition hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800/50"
                            >
                                <div class="flex min-w-[100px] flex-col text-sm">
                                    <flux:text class="font-medium">{{ $event->starts_at->format('H:i') }}</flux:text>
                                    @if ($event->ends_at)
                                        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ $event->ends_at->format('H:i') }}</flux:text>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <flux:text class="font-medium">{{ $event->title }}</flux:text>
                                    <flux:badge size="sm" :color="$event->isInterview() ? 'green' : 'blue'" class="mt-1">
                                        {{ $event->isInterview() ? __('meeting.type_interview') : __('meeting.type_internal') }}
                                    </flux:badge>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
            @if ($eventsByDate->isEmpty())
                <flux:callout variant="secondary" icon="calendar-days">
                    <flux:callout.text>{{ __('meeting.no_events_this_period') }}</flux:callout.text>
                </flux:callout>
            @endif
        </div>
    </flux:card>
</div>
