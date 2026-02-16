<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <flux:heading size="xl" level="1">{{ __('nav.candidates') }}</flux:heading>
            <flux:button icon="plus" :href="route('candidates.create')" wire:navigate variant="primary">
                {{ __('candidate.create') }}
            </flux:button>
        </div>

        <div wire:loading.class="opacity-50 pointer-events-none" class="relative">
            <flux:table :paginate="$candidates">
                <thead data-flux-columns>
                    <tr>
                        <flux:table.column
                            sortable
                            :sorted="$sortField === 'created_at'"
                            :direction="$sortDirection"
                            wire:click="sortBy('created_at')"
                        >
                            {{ __('candidate.name') }}
                        </flux:table.column>
                        <flux:table.column
                            sortable
                            :sorted="$sortField === 'position_id'"
                            :direction="$sortDirection"
                            wire:click="sortBy('position_id')"
                        >
                            {{ __('candidate.position') }}
                        </flux:table.column>
                        <flux:table.column
                            sortable
                            :sorted="$sortField === 'pipeline_stage_id'"
                            :direction="$sortDirection"
                            wire:click="sortBy('pipeline_stage_id')"
                        >
                            {{ __('candidate.stage') }}
                        </flux:table.column>
                        <flux:table.column
                            sortable
                            :sorted="$sortField === 'applied_at'"
                            :direction="$sortDirection"
                            wire:click="sortBy('applied_at')"
                        >
                            {{ __('candidate.applied_at') }}
                        </flux:table.column>
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
                                <flux:select wire:model.live="positionId" :placeholder="__('candidate.filter_position')" class="min-w-full max-w-[180px]">
                                    <flux:select.option value="">{{ __('candidate.all_positions') }}</flux:select.option>
                                    @foreach ($positions as $position)
                                        <flux:select.option :value="$position->id">{{ $position->title }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                        </th>
                        <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30">
                            <div class="flex flex-col gap-1">
                                <flux:select wire:model.live="pipelineStageId" :placeholder="__('candidate.filter_stage')" class="min-w-full max-w-[180px]">
                                    <flux:select.option value="">{{ __('candidate.all_stages') }}</flux:select.option>
                                    @foreach ($pipelineStages as $stage)
                                        <flux:select.option :value="$stage->id">{{ $stage->label() }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                        </th>
                        <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30">
                            <div class="flex flex-col gap-1">
                                <flux:input
                                    wire:model.live="appliedFrom"
                                    type="date"
                                    class="min-w-full max-w-[140px]"
                                />
                                <flux:input
                                    wire:model.live="appliedTo"
                                    type="date"
                                    class="min-w-full max-w-[140px]"
                                />
                                @if ($appliedFrom !== '' || $appliedTo !== '')
                                    <flux:button size="sm" variant="ghost" wire:click="clearAppliedRange" class="self-start">
                                        {{ __('common.clear') }}
                                    </flux:button>
                                @endif
                            </div>
                        </th>
                        <th class="py-2 px-3 first:ps-0 last:pe-0 text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                    </tr>
                </thead>
                <flux:table.rows>
                    @forelse ($candidates as $candidate)
                        <flux:table.row :key="$candidate->id">
                            <flux:table.cell>
                                <flux:link :href="route('candidates.show', $candidate)" wire:navigate class="font-medium">
                                    {{ $candidate->person->fullName() }}
                                </flux:link>
                                @if ($candidate->person->email)
                                    <flux:text class="text-zinc-500 dark:text-zinc-400 text-sm">{{ $candidate->person->email }}</flux:text>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>{{ $candidate->position->title }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" color="zinc" inset="top bottom">{{ $candidate->pipelineStage->label() }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap">
                                @if ($candidate->applied_at)
                                    {{ $candidate->applied_at->isoFormat('L') }}
                                @else
                                    —
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button size="sm" icon="eye" :href="route('candidates.show', $candidate)" wire:navigate variant="ghost" inset="top bottom">
                                    {{ __('common.view') }}
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell></flux:table.cell>
                            <flux:table.cell></flux:table.cell>
                            <flux:table.cell class="py-6">
                                <flux:callout variant="secondary" icon="user-group" class="max-w-md">
                                    <flux:callout.heading>{{ __('candidate.no_candidates') }}</flux:callout.heading>
                                    <flux:callout.text>{{ __('candidate.no_candidates_hint') }}</flux:callout.text>
                                    <x-slot name="actions">
                                        <flux:button size="sm" icon="plus" :href="route('candidates.create')" wire:navigate variant="primary">
                                            {{ __('candidate.create') }}
                                        </flux:button>
                                    </x-slot>
                                </flux:callout>
                            </flux:table.cell>
                            <flux:table.cell></flux:table.cell>
                            <flux:table.cell></flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
</div>
