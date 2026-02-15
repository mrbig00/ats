<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center gap-2">
            <flux:button variant="ghost" icon="arrow-left" :href="route('candidates.index')" wire:navigate>
                {{ __('common.back') }}
            </flux:button>
            <flux:heading size="xl" level="1">{{ __('candidate.create') }}</flux:heading>
        </div>

        <flux:card class="max-w-2xl">
            <form wire:submit="save" class="space-y-6">
                <flux:heading size="lg">{{ __('candidate.person_data') }}</flux:heading>
                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('candidate.first_name') }}</flux:label>
                        <flux:input wire:model="firstName" type="text" required />
                        <flux:error name="firstName" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('candidate.last_name') }}</flux:label>
                        <flux:input wire:model="lastName" type="text" required />
                        <flux:error name="lastName" />
                    </flux:field>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('candidate.email') }}</flux:label>
                        <flux:input wire:model="email" type="email" />
                        <flux:error name="email" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('candidate.phone') }}</flux:label>
                        <flux:input wire:model="phone" type="text" />
                        <flux:error name="phone" />
                    </flux:field>
                </div>

                <flux:separator />

                <flux:heading size="lg">{{ __('candidate.application_data') }}</flux:heading>
                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('candidate.position') }}</flux:label>
                        <flux:select wire:model="positionId" required>
                            <flux:select.option value="">{{ __('candidate.select_position') }}</flux:select.option>
                            @foreach ($positions as $position)
                                <flux:select.option :value="$position->id">{{ $position->title }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="positionId" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('candidate.stage') }}</flux:label>
                        <flux:select wire:model="pipelineStageId" required>
                            @foreach ($pipelineStages as $stage)
                                <flux:select.option :value="$stage->id">{{ $stage->label() }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="pipelineStageId" />
                    </flux:field>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('candidate.source') }}</flux:label>
                        <flux:input wire:model="source" type="text" />
                        <flux:error name="source" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('candidate.applied_at') }}</flux:label>
                        <flux:input wire:model="appliedAt" type="date" />
                        <flux:error name="appliedAt" />
                    </flux:field>
                </div>

                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        {{ __('common.save') }}
                    </flux:button>
                    <flux:button type="button" variant="ghost" :href="route('candidates.index')" wire:navigate>
                        {{ __('common.cancel') }}
                    </flux:button>
                </div>
            </form>
        </flux:card>
</div>
