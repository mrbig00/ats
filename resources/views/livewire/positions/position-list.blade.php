<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">{{ __('nav.jobs') }}</flux:heading>
        @can('create', \App\Models\Position::class)
            <flux:button icon="plus" :href="route('jobs.create')" wire:navigate variant="primary">
                {{ __('job.create') }}
            </flux:button>
        @endcan
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <flux:checkbox wire:model.live="includeArchived" :label="__('archive.include_archived')" />
        @can('exportCsv', \App\Models\Position::class)
            <flux:button size="sm" variant="ghost" icon="arrow-down-tray" :href="route('jobs.csv.template')">
                {{ __('common.download_template') }}
            </flux:button>
            <flux:button size="sm" variant="ghost" icon="arrow-down-on-square" :href="route('jobs.csv.export', request()->query())">
                {{ __('common.export_csv') }}
            </flux:button>
        @endcan
        @can('importCsv', \App\Models\Position::class)
            <flux:button size="sm" variant="ghost" icon="arrow-up-tray" wire:click="openCsvImport">
                {{ __('common.import_csv') }}
            </flux:button>
        @endcan
    </div>

    <div wire:loading.class="opacity-50 pointer-events-none" class="relative">
        <flux:table :paginate="$positions">
            <thead data-flux-columns>
                <tr>
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'title'"
                        :direction="$sortDirection"
                        wire:click="sortBy('title')"
                    >
                        {{ __('job.title') }}
                    </flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'status'"
                        :direction="$sortDirection"
                        wire:click="sortBy('status')"
                    >
                        {{ __('job.status') }}
                    </flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'opens_at'"
                        :direction="$sortDirection"
                        wire:click="sortBy('opens_at')"
                    >
                        {{ __('job.opens_at') }}
                    </flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'closes_at'"
                        :direction="$sortDirection"
                        wire:click="sortBy('closes_at')"
                    >
                        {{ __('job.closes_at') }}
                    </flux:table.column>
                    <flux:table.column>{{ __('job.open_days') }}</flux:table.column>
                    <flux:table.column>{{ __('job.funnel_kpis') }}</flux:table.column>
                    <flux:table.column>{{ __('job.candidates_count') }}</flux:table.column>
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
                            <flux:select wire:model.live="statusFilter" :disabled="! $includeArchived" :placeholder="__('job.filter_status')" class="min-w-full max-w-[180px]">
                                <flux:select.option value="open">{{ __('job.status_open') }}</flux:select.option>
                                <flux:select.option value="closed">{{ __('job.status_closed') }}</flux:select.option>
                            </flux:select>
                        </div>
                    </th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                </tr>
            </thead>
            <flux:table.rows>
                @forelse ($positions as $position)
                    <flux:table.row :key="$position->id">
                        <flux:table.cell>
                            <flux:link :href="route('jobs.show', $position)" wire:navigate class="font-medium">
                                {{ $position->title }}
                            </flux:link>
                            @if ($position->description)
                                <flux:text class="line-clamp-1 text-zinc-500 dark:text-zinc-400 text-sm">{{ Str::limit($position->description, 60) }}</flux:text>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" :color="$position->isOpen() ? 'green' : 'zinc'" inset="top bottom">
                                {{ $position->statusLabel() }}
                            </flux:badge>
                            @if ($position->urgency)
                                <flux:badge
                                    size="sm"
                                    :color="$position->urgency->badgeColor()"
                                    inset="top bottom"
                                    class="ms-1"
                                >
                                    {{ $position->urgency->label() }}
                                </flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">
                            @if ($position->opens_at)
                                {{ $position->opens_at->isoFormat('L') }}
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">
                            @if ($position->closes_at)
                                {{ $position->closes_at->isoFormat('L') }}
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">
                            <flux:text class="text-sm text-zinc-600 dark:text-zinc-300">
                                {{ __('job.open_for_days_short', ['days' => $position->openForDays()]) }}
                            </flux:text>
                            <flux:text class="text-sm text-zinc-600 dark:text-zinc-300">
                                @if ($position->closesInDaysForDisplay() !== null)
                                    {{ __('job.closes_in_days_short', ['days' => $position->closesInDaysForDisplay()]) }}
                                @else
                                    {{ __('common.em_dash') }}
                                @endif
                            </flux:text>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1">
                                <flux:badge size="sm" color="zinc" inset="top bottom" :title="__('job.kpi_applied')">
                                    {{ __('job.kpi_applied_short') }} {{ $position->funnel_applied_count }}
                                </flux:badge>
                                <flux:badge size="sm" color="zinc" inset="top bottom" :title="__('job.kpi_interview')">
                                    {{ __('job.kpi_interview_short') }} {{ $position->funnel_interview_count }}
                                </flux:badge>
                                <flux:badge size="sm" color="zinc" inset="top bottom" :title="__('job.kpi_offer')">
                                    {{ __('job.kpi_offer_short') }} {{ $position->funnel_offer_count }}
                                </flux:badge>
                                <flux:badge size="sm" color="zinc" inset="top bottom" :title="__('job.kpi_hired')">
                                    {{ __('job.kpi_hired_short') }} {{ $position->funnel_hired_count }}
                                </flux:badge>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>{{ $position->candidates_count }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="sm" icon="eye" :href="route('jobs.show', $position)" wire:navigate variant="ghost" inset="top bottom">
                                {{ __('common.view') }}
                            </flux:button>
                            @can('edit', $position)
                                <flux:button size="sm" icon="pencil" :href="route('jobs.edit', $position)" wire:navigate variant="ghost" inset="top bottom">
                                    {{ __('common.edit') }}
                                </flux:button>
                            @endcan
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell></flux:table.cell>
                        <flux:table.cell></flux:table.cell>
                        <flux:table.cell></flux:table.cell>
                        <flux:table.cell class="py-6">
                            <flux:callout variant="secondary" icon="briefcase" class="max-w-md">
                                <flux:callout.heading>{{ __('job.no_positions') }}</flux:callout.heading>
                                <flux:callout.text>{{ __('job.no_positions_hint') }}</flux:callout.text>
                                @can('create', \App\Models\Position::class)
                                    <x-slot name="actions">
                                        <flux:button size="sm" icon="plus" :href="route('jobs.create')" wire:navigate variant="primary">
                                            {{ __('job.create') }}
                                        </flux:button>
                                    </x-slot>
                                @endcan
                            </flux:callout>
                        </flux:table.cell>
                        <flux:table.cell></flux:table.cell>
                        <flux:table.cell></flux:table.cell>
                        <flux:table.cell></flux:table.cell>
                        <flux:table.cell></flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <flux:modal wire:model="csvImportOpen" class="sm:max-w-lg">
        <form wire:submit="importCsv" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ __('common.import_csv') }}</flux:heading>
                <flux:subheading class="mt-1">{{ __('job.csv_import_hint') }}</flux:subheading>
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
