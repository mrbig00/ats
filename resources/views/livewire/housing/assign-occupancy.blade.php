<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center gap-2">
        <flux:button variant="ghost" icon="arrow-left" :href="route('housing.apartments.show', $room->apartment)" wire:navigate>
            {{ __('common.back') }}
        </flux:button>
        <flux:heading size="xl" level="1">{{ __('housing.assign_employee') }}</flux:heading>
        <flux:text class="text-zinc-500 dark:text-zinc-400">— {{ $room->name }}, {{ $room->apartment->name }}</flux:text>
    </div>

    <div class="flex flex-1 items-center justify-center">
        <flux:card class="w-full max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <flux:field>
                <flux:label>{{ __('housing.employee') }}</flux:label>
                <flux:select wire:model="employeeId" required>
                    <flux:select.option value="">{{ __('housing.select_employee') }}</flux:select.option>
                    @foreach ($employees as $employee)
                        <flux:select.option :value="$employee->id">{{ $employee->person->fullName() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="employeeId" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('housing.occupancy_starts_at') }}</flux:label>
                <flux:input wire:model="startsAt" type="date" required />
                <flux:error name="startsAt" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('housing.occupancy_ends_at') }}</flux:label>
                <flux:input wire:model="endsAt" type="date" />
                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('housing.occupancy_ends_at_hint') }}</flux:text>
                <flux:error name="endsAt" />
            </flux:field>
            <div class="flex gap-2">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    {{ __('common.save') }}
                </flux:button>
                <flux:button type="button" variant="ghost" :href="route('housing.apartments.show', $room->apartment)" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
    </div>
</div>
