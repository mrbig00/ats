<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex flex-col gap-2">
        <flux:heading size="xl" level="1">{{ __('nav.archive') }}</flux:heading>
        <flux:callout variant="secondary" icon="information-circle">
            <flux:callout.text>{{ __('archive.business_rule') }}</flux:callout.text>
        </flux:callout>
    </div>

    <div class="flex flex-wrap gap-2">
        <flux:button
            wire:click="$set('panel', 'jobs')"
            :variant="$panel === 'jobs' ? 'primary' : 'outline'"
            size="sm"
        >
            {{ __('archive.tab_jobs') }}
        </flux:button>
        <flux:button
            wire:click="$set('panel', 'candidates')"
            :variant="$panel === 'candidates' ? 'primary' : 'outline'"
            size="sm"
        >
            {{ __('archive.tab_candidates') }}
        </flux:button>
    </div>

    <div wire:loading.class="opacity-50 pointer-events-none" class="relative">
        @if ($panel === 'jobs')
            <flux:table :paginate="$paginator">
                <thead data-flux-columns>
                    <tr>
                        <flux:table.column
                            sortable
                            :sorted="$jobsSortField === 'title'"
                            :direction="$jobsSortDirection"
                            wire:click="sortJobsBy('title')"
                        >
                            {{ __('job.title') }}
                        </flux:table.column>
                        <flux:table.column
                            sortable
                            :sorted="$jobsSortField === 'status'"
                            :direction="$jobsSortDirection"
                            wire:click="sortJobsBy('status')"
                        >
                            {{ __('job.status') }}
                        </flux:table.column>
                        <flux:table.column
                            sortable
                            :sorted="$jobsSortField === 'closes_at'"
                            :direction="$jobsSortDirection"
                            wire:click="sortJobsBy('closes_at')"
                        >
                            {{ __('job.closes_at') }}
                        </flux:table.column>
                        <flux:table.column>{{ __('job.candidates_count') }}</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </tr>
                    <tr>
                        <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30">
                            <flux:input
                                wire:model.live.debounce.300ms="jobSearch"
                                type="search"
                                :placeholder="__('common.search')"
                                class="min-w-full max-w-[220px]"
                            />
                        </th>
                        <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                        <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                        <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                        <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                    </tr>
                </thead>
                <flux:table.rows>
                    @forelse ($paginator as $position)
                        <flux:table.row :key="$position->id">
                            <flux:table.cell>
                                <flux:link :href="route('jobs.show', $position)" wire:navigate class="font-medium">
                                    {{ $position->title }}
                                </flux:link>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" :color="$position->isOpen() ? 'green' : 'zinc'" inset="top bottom">
                                    {{ $position->statusLabel() }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap">
                                @if ($position->closes_at)
                                    {{ $position->closes_at->isoFormat('L') }}
                                @else
                                    —
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>{{ $position->candidates_count }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:button size="sm" icon="eye" :href="route('jobs.show', $position)" wire:navigate variant="ghost" inset="top bottom">
                                    {{ __('common.view') }}
                                </flux:button>
                                @can('reopen', $position)
                                    <flux:button
                                        size="sm"
                                        icon="arrow-path"
                                        variant="ghost"
                                        inset="top bottom"
                                        wire:click="reopenPosition({{ $position->id }})"
                                        wire:confirm="{{ __('archive.reopen_confirm') }}"
                                    >
                                        {{ __('archive.reopen') }}
                                    </flux:button>
                                @endcan
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="py-8">
                                <flux:callout variant="secondary" icon="archive-box">
                                    <flux:callout.heading>{{ __('archive.no_expired_jobs') }}</flux:callout.heading>
                                </flux:callout>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        @else
            <flux:table :paginate="$paginator">
                <thead data-flux-columns>
                    <tr>
                        <flux:table.column
                            sortable
                            :sorted="$candidatesSortField === 'created_at'"
                            :direction="$candidatesSortDirection"
                            wire:click="sortCandidatesBy('created_at')"
                        >
                            {{ __('candidate.name') }}
                        </flux:table.column>
                        <flux:table.column
                            sortable
                            :sorted="$candidatesSortField === 'position_id'"
                            :direction="$candidatesSortDirection"
                            wire:click="sortCandidatesBy('position_id')"
                        >
                            {{ __('candidate.position') }}
                        </flux:table.column>
                        <flux:table.column
                            sortable
                            :sorted="$candidatesSortField === 'pipeline_stage_id'"
                            :direction="$candidatesSortDirection"
                            wire:click="sortCandidatesBy('pipeline_stage_id')"
                        >
                            {{ __('candidate.stage') }}
                        </flux:table.column>
                        <flux:table.column
                            sortable
                            :sorted="$candidatesSortField === 'applied_at'"
                            :direction="$candidatesSortDirection"
                            wire:click="sortCandidatesBy('applied_at')"
                        >
                            {{ __('candidate.applied_at') }}
                        </flux:table.column>
                        <flux:table.column></flux:table.column>
                    </tr>
                    <tr>
                        <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30">
                            <flux:input
                                wire:model.live.debounce.300ms="candidateSearch"
                                type="search"
                                :placeholder="__('common.search')"
                                class="min-w-full max-w-[220px]"
                            />
                        </th>
                        <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                        <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                        <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                        <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                    </tr>
                </thead>
                <flux:table.rows>
                    @forelse ($paginator as $candidate)
                        <flux:table.row :key="$candidate->id">
                            <flux:table.cell>
                                <flux:link :href="route('candidates.show', $candidate)" wire:navigate class="font-medium">
                                    {{ $candidate->person->fullName() }}
                                </flux:link>
                            </flux:table.cell>
                            <flux:table.cell>{{ $candidate->position->title }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" color="zinc" inset="top bottom">{{ $candidate->pipelineStage->label() }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap">
                                @if ($candidate->applied_at)
                                    {{ $candidate->applied_at->isoFormat('L LT') }}
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
                            <flux:table.cell colspan="5" class="py-8">
                                <flux:callout variant="secondary" icon="archive-box">
                                    <flux:callout.heading>{{ __('archive.no_archived_candidates') }}</flux:callout.heading>
                                </flux:callout>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        @endif
    </div>
</div>
