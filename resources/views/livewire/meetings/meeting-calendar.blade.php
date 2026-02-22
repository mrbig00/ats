<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <flux:button variant="ghost" icon="arrow-left" :href="route('meetings.index')" wire:navigate>
                {{ __('common.back') }}
            </flux:button>
            <flux:heading size="xl" level="1">{{ __('meeting.view_calendar') }}</flux:heading>
        </div>
        @can('create', \App\Models\CalendarEvent::class)
            <flux:button icon="plus" :href="route('meetings.create')" wire:navigate variant="primary">
                {{ __('meeting.create') }}
            </flux:button>
        @endcan
    </div>

    <flux:card class="overflow-hidden">
        <div
            id="meeting-calendar"
            class="fc p-4"
            data-api-url="{{ route('meetings.calendar.events') }}"
            data-meeting-show-base-url="{{ url('/meetings') }}"
            data-locale="{{ str_replace('_', '-', app()->getLocale()) }}"
            data-create-meeting-url="{{ auth()->user()?->can('create', \App\Models\CalendarEvent::class) ? route('meetings.create') : '' }}"
        ></div>
    </flux:card>
</div>
