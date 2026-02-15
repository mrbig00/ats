<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <flux:heading size="xl" level="1">{{ __('nav.candidates') }}</flux:heading>
            <flux:button icon="plus" :href="route('candidates.create')" wire:navigate variant="primary">
                {{ __('candidate.create') }}
            </flux:button>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <flux:input
                wire:model.live.debounce.300ms="search"
                type="search"
                :placeholder="__('common.search')"
                class="min-w-[200px]"
            />
            <flux:select wire:model.live="pipelineStageId" :placeholder="__('candidate.filter_stage')" class="min-w-[180px]">
                <flux:select.option value="">{{ __('candidate.all_stages') }}</flux:select.option>
                @foreach ($pipelineStages as $stage)
                    <flux:select.option :value="$stage->id">{{ $stage->label() }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="positionId" :placeholder="__('candidate.filter_position')" class="min-w-[180px]">
                <flux:select.option value="">{{ __('candidate.all_positions') }}</flux:select.option>
                @foreach ($positions as $position)
                    <flux:select.option :value="$position->id">{{ $position->title }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <flux:card class="overflow-hidden p-0">
            <flux:table>
                <flux:table.columns>
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
                    <flux:table.column>{{ __('candidate.applied_at') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
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
                                <flux:badge size="sm" color="zinc">{{ $candidate->pipelineStage->label() }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if ($candidate->applied_at)
                                    {{ $candidate->applied_at->isoFormat('L') }}
                                @else
                                    —
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button size="sm" icon="eye" :href="route('candidates.show', $candidate)" wire:navigate variant="ghost">
                                    {{ __('common.view') }}
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center text-zinc-500 dark:text-zinc-400">
                                {{ __('candidate.no_candidates') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
            @if ($candidates->hasPages())
                <div class="border-t border-zinc-200 px-4 py-3 dark:border-zinc-700">
                    {{ $candidates->links() }}
                </div>
            @endif
        </flux:card>
</div>
