<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">{{ __('nav.employees') }}</flux:heading>
    </div>

    <div wire:loading.class="opacity-50 pointer-events-none" class="relative">
        <flux:table :paginate="$employees">
            <thead data-flux-columns>
                <tr>
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'name'"
                        :direction="$sortDirection"
                        wire:click="sortBy('name')"
                    >
                        {{ __('employee.name') }}
                    </flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'status'"
                        :direction="$sortDirection"
                        wire:click="sortBy('status')"
                    >
                        {{ __('employee.status') }}
                    </flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'entry_date'"
                        :direction="$sortDirection"
                        wire:click="sortBy('entry_date')"
                    >
                        {{ __('employee.entry_date') }}
                    </flux:table.column>
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'exit_date'"
                        :direction="$sortDirection"
                        wire:click="sortBy('exit_date')"
                    >
                        {{ __('employee.exit_date') }}
                    </flux:table.column>
                    <flux:table.column></flux:table.column>
                </tr>
                <tr>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30">
                        <flux:input
                            wire:model.live.debounce.300ms="search"
                            type="search"
                            :placeholder="__('common.search')"
                            class="min-w-full max-w-[200px]"
                        />
                    </th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30">
                        <flux:select wire:model.live="status" :placeholder="__('employee.filter_status')" class="min-w-full max-w-[160px]">
                            <flux:select.option value="">{{ __('employee.all_statuses') }}</flux:select.option>
                            <flux:select.option value="active">{{ __('employee.status_active') }}</flux:select.option>
                            <flux:select.option value="leaving">{{ __('employee.status_leaving') }}</flux:select.option>
                            <flux:select.option value="left">{{ __('employee.status_left') }}</flux:select.option>
                        </flux:select>
                    </th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-start text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                    <th class="py-2 px-3 first:ps-0 last:pe-0 text-sm border-b border-zinc-800/10 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-800/30"></th>
                </tr>
            </thead>
            <flux:table.rows>
                @forelse ($employees as $employee)
                    <flux:table.row :key="$employee->id">
                        <flux:table.cell>
                            <flux:link :href="route('employees.show', $employee)" wire:navigate class="font-medium">
                                {{ $employee->person->fullName() }}
                            </flux:link>
                            @if ($employee->person->email)
                                <flux:text class="text-zinc-500 dark:text-zinc-400 text-sm">{{ $employee->person->email }}</flux:text>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="zinc" inset="top bottom">{{ __('employee.status_' . $employee->status) }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">
                            @if ($employee->entry_date)
                                {{ $employee->entry_date->isoFormat('L') }}
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">
                            @if ($employee->exit_date)
                                {{ $employee->exit_date->isoFormat('L') }}
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="sm" icon="eye" :href="route('employees.show', $employee)" wire:navigate variant="ghost" inset="top bottom">
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
                                <flux:callout.heading>{{ __('employee.no_employees') }}</flux:callout.heading>
                                <flux:callout.text>{{ __('employee.no_employees_hint') }}</flux:callout.text>
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
