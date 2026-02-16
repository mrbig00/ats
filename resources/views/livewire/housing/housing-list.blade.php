<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">{{ __('nav.housing') }}</flux:heading>
        @can('create', \App\Models\Apartment::class)
            <flux:button icon="plus" :href="route('housing.apartments.create')" wire:navigate variant="primary">
                {{ __('housing.create_apartment') }}
            </flux:button>
        @endcan
    </div>

    <div class="relative">
        @forelse ($apartments as $apartment)
            <flux:card class="mb-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <flux:link :href="route('housing.apartments.show', $apartment)" wire:navigate class="text-lg font-medium">
                            {{ $apartment->name }}
                        </flux:link>
                        @if ($apartment->address)
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ $apartment->address }}</flux:text>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:badge size="sm" color="zinc" inset="top bottom">
                            {{ __('housing.rooms_count', ['count' => $apartment->rooms_count]) }}
                        </flux:badge>
                        <flux:button size="sm" icon="eye" :href="route('housing.apartments.show', $apartment)" wire:navigate variant="ghost">
                            {{ __('common.view') }}
                        </flux:button>
                        @can('update', $apartment)
                            <flux:button size="sm" icon="pencil" :href="route('housing.apartments.edit', $apartment)" wire:navigate variant="ghost">
                                {{ __('common.edit') }}
                            </flux:button>
                        @endcan
                    </div>
                </div>
            </flux:card>
        @empty
            <flux:callout variant="secondary" icon="building-office-2" class="max-w-md">
                <flux:callout.heading>{{ __('housing.no_apartments') }}</flux:callout.heading>
                <flux:callout.text>{{ __('housing.no_apartments_hint') }}</flux:callout.text>
                @can('create', \App\Models\Apartment::class)
                    <x-slot name="actions">
                        <flux:button size="sm" icon="plus" :href="route('housing.apartments.create')" wire:navigate variant="primary">
                            {{ __('housing.create_apartment') }}
                        </flux:button>
                    </x-slot>
                @endcan
            </flux:callout>
        @endforelse
    </div>
</div>
