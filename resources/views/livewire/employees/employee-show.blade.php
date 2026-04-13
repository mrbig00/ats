<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <flux:button variant="ghost" icon="arrow-left" :href="route('employees.index')" wire:navigate>
                {{ __('common.back') }}
            </flux:button>
            <flux:heading size="xl" level="1">{{ $employee->person->fullName() }}</flux:heading>
        </div>
        <div class="flex flex-wrap gap-2">
            @can('terminate', $employee)
                @if ($employee->isActive())
                    <flux:button variant="outline" icon="arrow-right-end-on-rectangle" wire:click="openTerminateModal">
                        {{ __('employee.terminate') }}
                    </flux:button>
                @endif
            @endcan
        </div>
    </div>

    <div class="flex flex-1 items-center justify-center">
        <div class="grid w-full max-w-2xl lg:grid-cols-4">
        <div class="lg:col-span-2 space-y-6">
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('employee.details') }}</flux:heading>
                <dl class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('employee.email') }}</flux:text>
                        <flux:text>{{ $employee->person->email ?? '—' }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('employee.phone') }}</flux:text>
                        <flux:text>{{ $employee->person->phone ?? '—' }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('employee.status') }}</flux:text>
                        <flux:badge size="sm" color="zinc" inset="top bottom">{{ __('employee.status_' . $employee->status) }}</flux:badge>
                    </div>
                    <div>
                        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('employee.entry_date') }}</flux:text>
                        @can('update', $employee)
                            <form wire:submit="saveEntryDate" class="flex items-center gap-2 mt-1">
                                <flux:input wire:model="entryDate" type="date" class="max-w-[160px]" />
                                <flux:button type="submit" size="sm" wire:loading.attr="disabled">{{ __('common.save') }}</flux:button>
                            </form>
                        @else
                            <flux:text>{{ $employee->entry_date?->isoFormat('L') ?? '—' }}</flux:text>
                        @endcan
                    </div>
                    @if ($employee->exit_date)
                        <div>
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('employee.exit_date') }}</flux:text>
                            <flux:text>{{ $employee->exit_date->isoFormat('L') }}</flux:text>
                        </div>
                    @endif
                    @cannot('update', $employee)
                        <div>
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.nationality') }}</flux:text>
                            <flux:text>{{ $employee->nationality ?? '—' }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.driving_license_category') }}</flux:text>
                            <flux:text>{{ $employee->driving_license_category ?? '—' }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.has_own_car') }}</flux:text>
                            <flux:text>@if($employee->has_own_car === null){{ '—' }}@elseif($employee->has_own_car){{ __('common.yes') }}@else{{ __('common.no') }}@endif</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.german_level') }}</flux:text>
                            <flux:text>{{ $employee->german_level?->label() ?? '—' }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.available_from') }}</flux:text>
                            <flux:text>{{ $employee->available_from?->isoFormat('L') ?? '—' }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.housing_needed') }}</flux:text>
                            <flux:text>@if($employee->housing_needed === null){{ '—' }}@elseif($employee->housing_needed){{ __('common.yes') }}@else{{ __('common.no') }}@endif</flux:text>
                        </div>
                    @endcannot
                </dl>
            </flux:card>

            @can('update', $employee)
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('employee.profile_section') }}</flux:heading>
                    <form wire:submit="saveProfile" class="space-y-4">
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
                        <flux:button type="submit" size="sm" variant="primary" wire:loading.attr="disabled">{{ __('employee.save_profile') }}</flux:button>
                    </form>
                </flux:card>
            @endcan

            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('employee.housing') }}</flux:heading>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-3">{{ __('employee.housing_hint') }}</p>
                <ul class="space-y-2">
                    @forelse ($activeOccupancies as $occupancy)
                        <li class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 dark:border-zinc-700" wire:key="occ-{{ $occupancy->id }}">
                            <div>
                                <flux:link :href="route('housing.apartments.show', $occupancy->room->apartment)" wire:navigate class="font-medium">
                                    {{ $occupancy->room->apartment->name }} → {{ $occupancy->room->name }}
                                </flux:link>
                                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ __('housing.since') }} {{ $occupancy->starts_at->isoFormat('L') }}
                                </flux:text>
                            </div>
                            <flux:button size="sm" variant="ghost" :href="route('housing.apartments.show', $occupancy->room->apartment)" wire:navigate>
                                {{ __('common.view') }}
                            </flux:button>
                        </li>
                    @empty
                        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('housing.no_occupancy') }}</flux:text>
                    @endforelse
                </ul>
                <flux:button size="sm" variant="outline" :href="route('housing.index')" wire:navigate class="mt-3">
                    {{ __('employee.assign_room_via_housing') }}
                </flux:button>
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('employee.contracts') }}</flux:heading>
                @can('update', $employee)
                    <flux:button size="sm" icon="plus" wire:click="openContractModal" class="mb-4">
                        {{ __('contract.add') }}
                    </flux:button>
                @endcan
                <ul class="space-y-2">
                    @forelse ($employee->contracts as $contract)
                        <li class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 dark:border-zinc-700" wire:key="contract-{{ $contract->id }}">
                            <div>
                                <flux:text class="font-medium">{{ $contract->type }}</flux:text>
                                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $contract->starts_at->isoFormat('L') }}
                                    @if ($contract->ends_at)
                                        – {{ $contract->ends_at->isoFormat('L') }}
                                    @else
                                        ({{ __('contract.open_ended') }})
                                    @endif
                                </flux:text>
                                @if ($contract->notes)
                                    <flux:text class="text-sm mt-1">{{ $contract->notes }}</flux:text>
                                @endif
                            </div>
                            @can('update', $contract)
                                <div class="flex gap-1">
                                    <flux:button size="sm" variant="ghost" wire:click="openEditContractModal({{ $contract->id }})">
                                        {{ __('common.edit') }}
                                    </flux:button>
                                    <flux:button size="sm" variant="ghost" wire:click="deleteContract({{ $contract->id }})" wire:confirm="{{ __('contract.confirm_delete') }}">
                                        {{ __('common.delete') }}
                                    </flux:button>
                                </div>
                            @endcan
                        </li>
                    @empty
                        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('contract.no_contracts') }}</flux:text>
                    @endforelse
                </ul>
            </flux:card>
        </div>
        </div>
    </div>

    <flux:modal wire:model="showTerminateModal" class="space-y-4">
        <flux:heading size="lg">{{ __('employee.terminate_confirm_title') }}</flux:heading>
        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('employee.terminate_confirm_text') }}</flux:text>
        <form wire:submit="terminate" class="space-y-4">
            <flux:field>
                <flux:label>{{ __('employee.exit_date') }}</flux:label>
                <flux:input type="date" wire:model="terminateExitDate" />
                <flux:error name="terminateExitDate" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('employee.status') }}</flux:label>
                <flux:select wire:model="terminateStatus">
                    <flux:select.option value="leaving">{{ __('employee.status_leaving') }}</flux:select.option>
                    <flux:select.option value="left">{{ __('employee.status_left') }}</flux:select.option>
                </flux:select>
                <flux:error name="terminateStatus" />
            </flux:field>
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showTerminateModal', false)">{{ __('common.cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">{{ __('employee.terminate') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal wire:model="showContractModal" class="space-y-4">
        <flux:heading size="lg">{{ __('contract.add') }}</flux:heading>
        <form wire:submit="addContract" class="space-y-4">
            <flux:field>
                <flux:label>{{ __('contract.type') }}</flux:label>
                <flux:input wire:model="contractType" :placeholder="__('contract.type_placeholder')" />
                <flux:error name="contractType" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('contract.starts_at') }}</flux:label>
                <flux:input type="date" wire:model="contractStartsAt" />
                <flux:error name="contractStartsAt" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('contract.ends_at') }}</flux:label>
                <flux:input type="date" wire:model="contractEndsAt" :placeholder="__('contract.ends_at_optional')" />
                <flux:error name="contractEndsAt" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('contract.notes') }}</flux:label>
                <flux:textarea wire:model="contractNotes" rows="2" />
                <flux:error name="contractNotes" />
            </flux:field>
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showContractModal', false)">{{ __('common.cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">{{ __('common.save') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal wire:model="showEditContractModal" class="space-y-4">
        <flux:heading size="lg">{{ __('contract.edit') }}</flux:heading>
        <form wire:submit="updateContract" class="space-y-4">
            <flux:field>
                <flux:label>{{ __('contract.type') }}</flux:label>
                <flux:input wire:model="editContractType" />
                <flux:error name="editContractType" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('contract.starts_at') }}</flux:label>
                <flux:input type="date" wire:model="editContractStartsAt" />
                <flux:error name="editContractStartsAt" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('contract.ends_at') }}</flux:label>
                <flux:input type="date" wire:model="editContractEndsAt" />
                <flux:error name="editContractEndsAt" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('contract.notes') }}</flux:label>
                <flux:textarea wire:model="editContractNotes" rows="2" />
                <flux:error name="editContractNotes" />
            </flux:field>
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showEditContractModal', false)">{{ __('common.cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">{{ __('common.save') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
