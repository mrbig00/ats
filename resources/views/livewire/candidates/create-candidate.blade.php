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

                <flux:separator />

                <flux:heading size="lg">{{ __('candidate.profile_section') }}</flux:heading>
                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('candidate.nationality') }}</flux:label>
                        <flux:input wire:model="nationality" type="text" />
                        <flux:error name="nationality" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('candidate.driving_license_category') }}</flux:label>
                        <flux:input wire:model="driving_license_category" type="text" />
                        <flux:error name="driving_license_category" />
                    </flux:field>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('candidate.has_own_car') }}</flux:label>
                        <flux:select wire:model="has_own_car">
                            <flux:select.option value="">{{ __('common.not_specified') }}</flux:select.option>
                            <flux:select.option value="1">{{ __('common.yes') }}</flux:select.option>
                            <flux:select.option value="0">{{ __('common.no') }}</flux:select.option>
                        </flux:select>
                        <flux:error name="has_own_car" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('candidate.german_level') }}</flux:label>
                        <flux:select wire:model="german_level">
                            <flux:select.option value="">{{ __('common.not_specified') }}</flux:select.option>
                            @foreach ($germanLevels as $level)
                                <flux:select.option :value="$level->value">{{ $level->label() }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="german_level" />
                    </flux:field>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('candidate.available_from') }}</flux:label>
                        <flux:input wire:model="available_from" type="date" />
                        <flux:error name="available_from" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('candidate.housing_needed') }}</flux:label>
                        <flux:select wire:model="housing_needed">
                            <flux:select.option value="">{{ __('common.not_specified') }}</flux:select.option>
                            <flux:select.option value="1">{{ __('common.yes') }}</flux:select.option>
                            <flux:select.option value="0">{{ __('common.no') }}</flux:select.option>
                        </flux:select>
                        <flux:error name="housing_needed" />
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
