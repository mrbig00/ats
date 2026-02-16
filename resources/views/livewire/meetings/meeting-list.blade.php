<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <flux:heading size="xl" level="1">{{ __('nav.meetings') }}</flux:heading>
            <flux:button size="sm" icon="calendar-days" :href="route('meetings.calendar')" wire:navigate variant="outline">
                {{ __('meeting.view_calendar') }}
            </flux:button>
        </div>
        @can('create', \App\Models\CalendarEvent::class)
            <flux:button icon="plus" :href="route('meetings.create')" wire:navigate variant="primary">
                {{ __('meeting.create') }}
            </flux:button>
        @endcan
    </div>

    <div wire:loading.class="opacity-50 pointer-events-none" class="relative">
        <flux:table :paginate="$meetings">
            <thead data-flux-columns>
                <tr>
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'title'"
                        :direction="$sortDirection"
                        wire:click="sortBy('title')"
                    >
                        {{ __('meeting.title') }}
                    </flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'type'"
                        :direction="$sortDirection"
                        wire:click="sortBy('type')"
                    >
                        {{ __('meeting.type') }}
                    </flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'starts_at'"
                        :direction="$sortDirection"
                        wire:click="sortBy('starts_at')"
                    >
                        {{ __('meeting.starts_at') }}
                    </flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'ends_at'"
                        :direction="$sortDirection"
                        wire:click="sortBy('ends_at')"
                    >
                        {{ __('meeting.ends_at') }}
                    </flux:table.column>
                    <flux:table.column>{{ __('meeting.candidate') }}</flux:table.column>
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
                            <flux:select wire:model.live="typeFilter" :placeholder="__('meeting.filter_type')" class="min-w-full max-w-[180px]">
                                <flux:select.option value="">{{ __('meeting.all_types') }}</flux:select.option>
                                <flux:select.option value="internal_meeting">{{ __('meeting.type_internal') }}</flux:select.option>
                                <flux:select.option value="interview">{{ __('meeting.type_interview') }}</flux:select.option>
                            </flux:select>
                        </div>
                    </th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                </tr>
            </thead>
            <flux:table.rows>
                @forelse ($meetings as $meeting)
                    <flux:table.row :key="$meeting->id">
                        <flux:table.cell>
                            <flux:link :href="route('meetings.show', $meeting)" wire:navigate class="font-medium">
                                {{ $meeting->title }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" :color="$meeting->isInterview() ? 'green' : 'blue'" inset="top bottom">
                                {{ $meeting->isInterview() ? __('meeting.type_interview') : __('meeting.type_internal') }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">
                            {{ $meeting->starts_at->isoFormat('L LT') }}
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">
                            {{ $meeting->ends_at?->isoFormat('L LT') ?? '—' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($meeting->candidate)
                                <flux:link :href="route('candidates.show', $meeting->candidate)" wire:navigate class="text-sm">
                                    {{ $meeting->candidate->person->fullName() }}
                                </flux:link>
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="sm" icon="eye" :href="route('meetings.show', $meeting)" wire:navigate variant="ghost" inset="top bottom">
                                {{ __('common.view') }}
                            </flux:button>
                            @can('update', $meeting)
                                <flux:button size="sm" icon="pencil" :href="route('meetings.edit', $meeting)" wire:navigate variant="ghost" inset="top bottom">
                                    {{ __('common.edit') }}
                                </flux:button>
                            @endcan
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="py-8 text-center">
                            <flux:callout variant="secondary" icon="calendar-days" class="mx-auto max-w-md">
                                <flux:callout.heading>{{ __('meeting.no_meetings') }}</flux:callout.heading>
                                <flux:callout.text>{{ __('meeting.no_meetings_hint') }}</flux:callout.text>
                                @can('create', \App\Models\CalendarEvent::class)
                                    <x-slot name="actions">
                                        <flux:button size="sm" icon="plus" :href="route('meetings.create')" wire:navigate variant="primary">
                                            {{ __('meeting.create') }}
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
