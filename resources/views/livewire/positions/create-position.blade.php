<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center gap-2">
        <flux:button variant="ghost" icon="arrow-left" :href="route('jobs.index')" wire:navigate>
            {{ __('common.back') }}
        </flux:button>
        <flux:heading size="xl" level="1">{{ __('job.create') }}</flux:heading>
    </div>
    <div class="flex items-center justify-center">
    <flux:card class="max-w-3xl">
        <form wire:submit="save" class="space-y-6">
            <flux:field>
                <flux:label>{{ __('job.title') }}</flux:label>
                <flux:input wire:model="title" type="text" required />
                <flux:error name="title" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('job.description') }}</flux:label>
                <flux:textarea wire:model="description" rows="4" />
                <flux:error name="description" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('job.status') }}</flux:label>
                <flux:select wire:model="status">
                    <flux:select.option value="open">{{ __('job.status_open') }}</flux:select.option>
                    <flux:select.option value="closed">{{ __('job.status_closed') }}</flux:select.option>
                </flux:select>
                <flux:error name="status" />
            </flux:field>
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('job.opens_at') }}</flux:label>
                    <flux:input wire:model="opensAt" type="date" />
                    <flux:error name="opensAt" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('job.closes_at') }}</flux:label>
                    <flux:input wire:model="closesAt" type="date" />
                    <flux:error name="closesAt" />
                </flux:field>
            </div>
            <div class="flex gap-2">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    {{ __('common.save') }}
                </flux:button>
                <flux:button type="button" variant="ghost" :href="route('jobs.index')" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
    </div>
</div>
