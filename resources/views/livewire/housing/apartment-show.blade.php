<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <flux:button variant="ghost" icon="arrow-left" :href="route('housing.index')" wire:navigate>
                {{ __('common.back') }}
            </flux:button>
            <flux:heading size="xl" level="1">{{ $apartment->name }}</flux:heading>
        </div>
        <div class="flex gap-2">
            @can('update', $apartment)
                <flux:button icon="pencil" :href="route('housing.apartments.edit', $apartment)" wire:navigate variant="ghost">
                    {{ __('common.edit') }}
                </flux:button>
            @endcan
            @can('create', \App\Models\Room::class)
                <flux:button icon="plus" :href="route('housing.apartments.rooms.create', $apartment)" wire:navigate variant="primary">
                    {{ __('housing.add_room') }}
                </flux:button>
            @endcan
        </div>
    </div>

    @if ($apartment->address)
        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ $apartment->address }}</flux:text>
    @endif

    <div class="grid gap-4">
        @forelse ($rooms as $room)
            <flux:card>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <flux:heading size="lg" level="2">{{ $room->name }}</flux:heading>
                            @can('update', $room)
                                <flux:button size="sm" icon="pencil" :href="route('housing.rooms.edit', $room)" wire:navigate variant="ghost" inset="top bottom">
                                    {{ __('common.edit') }}
                                </flux:button>
                            @endcan
                            @can('create', \App\Models\Occupancy::class)
                                <flux:button size="sm" icon="user-plus" :href="route('housing.rooms.assign', $room)" wire:navigate variant="ghost" inset="top bottom">
                                    {{ __('housing.assign_employee') }}
                                </flux:button>
                            @endcan
                        </div>
                        @if ($room->notes)
                            <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $room->notes }}</flux:text>
                        @endif
                    </div>
                </div>
                <div class="mt-3 border-t border-zinc-200 dark:border-zinc-700 pt-3">
                    @php
                        $activeOccupancies = $room->occupancies->where('ends_at', null);
                        $pastOccupancies = $room->occupancies->where('ends_at', '!=', null);
                    @endphp
                    @if ($activeOccupancies->isNotEmpty())
                        <flux:heading size="sm" class="mb-2">{{ __('housing.current_occupancy') }}</flux:heading>
                        <ul class="space-y-1">
                            @foreach ($activeOccupancies as $occupancy)
                                <li class="flex items-center justify-between text-sm">
                                    <span>{{ $occupancy->employee->person->fullName() }}</span>
                                    <span class="text-zinc-500 dark:text-zinc-400">{{ __('housing.since') }} {{ $occupancy->starts_at->isoFormat('L') }}</span>
                                    @can('update', $occupancy)
                                        <flux:button size="xs" variant="ghost" wire:click="openEndOccupancyModal({{ $occupancy->id }})">
                                            {{ __('housing.end_occupancy') }}
                                        </flux:button>
                                    @endcan
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    @if ($pastOccupancies->isNotEmpty())
                        <flux:heading size="sm" class="mt-3 mb-2">{{ __('housing.past_occupancy') }}</flux:heading>
                        <ul class="space-y-1 text-sm text-zinc-500 dark:text-zinc-400">
                            @foreach ($pastOccupancies->take(5) as $occupancy)
                                <li>{{ $occupancy->employee->person->fullName() }} — {{ $occupancy->starts_at->isoFormat('L') }} – {{ $occupancy->ends_at?->isoFormat('L') }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if ($activeOccupancies->isEmpty() && $pastOccupancies->isEmpty())
                        <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('housing.no_occupancy') }}</flux:text>
                    @endif
                </div>
            </flux:card>
        @empty
            <flux:callout variant="secondary" icon="home" class="max-w-md">
                <flux:callout.heading>{{ __('housing.no_rooms') }}</flux:callout.heading>
                <flux:callout.text>{{ __('housing.no_rooms_hint') }}</flux:callout.text>
                @can('create', \App\Models\Room::class)
                    <x-slot name="actions">
                        <flux:button size="sm" icon="plus" :href="route('housing.apartments.rooms.create', $apartment)" wire:navigate variant="primary">
                            {{ __('housing.add_room') }}
                        </flux:button>
                    </x-slot>
                @endcan
            </flux:callout>
        @endforelse
    </div>

    @can('create', \App\Models\Occupancy::class)
        @livewire('housing.end-occupancy-modal')
    @endcan
</div>
