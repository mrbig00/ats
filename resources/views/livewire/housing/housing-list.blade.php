<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">{{ __('nav.housing') }}</flux:heading>
        <div class="flex flex-wrap items-center gap-2">
            @can('exportCsv', \App\Models\Apartment::class)
                <flux:button size="sm" variant="ghost" icon="arrow-down-tray" :href="route('housing.apartments.csv.template')">
                    {{ __('common.download_template') }}
                </flux:button>
                <flux:button size="sm" variant="ghost" icon="arrow-down-on-square" :href="route('housing.apartments.csv.export')">
                    {{ __('common.export_csv') }}
                </flux:button>
            @endcan
            @can('importCsv', \App\Models\Apartment::class)
                <flux:button size="sm" variant="ghost" icon="arrow-up-tray" wire:click="openCsvImport">
                    {{ __('common.import_csv') }}
                </flux:button>
            @endcan
            @can('create', \App\Models\Apartment::class)
                <flux:button icon="plus" :href="route('housing.apartments.create')" wire:navigate variant="primary">
                    {{ __('housing.create_apartment') }}
                </flux:button>
            @endcan
        </div>
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

    <flux:modal wire:model="csvImportOpen" class="sm:max-w-lg">
        <form wire:submit="importCsv" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ __('common.import_csv') }}</flux:heading>
                <flux:subheading class="mt-1">{{ __('housing.csv_import_hint') }}</flux:subheading>
            </div>

            <flux:field>
                <flux:label>{{ __('common.csv') }}</flux:label>
                <flux:input type="file" wire:model="csvFile" accept=".csv,text/csv" />
                <flux:error name="csvFile" />
            </flux:field>

            @if ($csvImportResult)
                <div class="rounded-lg border border-zinc-200 bg-white p-3 text-sm dark:border-white/10 dark:bg-zinc-900">
                    <div class="font-medium">{{ __('common.import_results') }}</div>
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        <div>{{ __('common.total_rows') }}: {{ $csvImportResult['totalRows'] }}</div>
                        <div>{{ __('common.import_failed') }}: {{ $csvImportResult['failedCount'] }}</div>
                        <div>{{ __('common.import_created') }}: {{ $csvImportResult['createdCount'] }}</div>
                        <div>{{ __('common.import_updated') }}: {{ $csvImportResult['updatedCount'] }}</div>
                    </div>

                    @if (! empty($csvImportResult['failures']))
                        <div class="mt-3">
                            <div class="font-medium">{{ __('common.failures') }}</div>
                            <ul class="mt-2 space-y-2">
                                @foreach (collect($csvImportResult['failures'])->take(10) as $failure)
                                    <li class="text-zinc-600 dark:text-zinc-300">
                                        <span class="font-medium">{{ __('common.row') }} {{ $failure['row'] }}:</span>
                                        {{ implode('; ', $failure['messages']) }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="closeCsvImport">
                    {{ __('common.close') }}
                </flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    {{ __('common.import') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
