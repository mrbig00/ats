<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center gap-2">
        <flux:button variant="ghost" icon="arrow-left" :href="route('housing.index')" wire:navigate>
            {{ __('common.back') }}
        </flux:button>
        <flux:heading size="xl" level="1">{{ __('housing.create_apartment') }}</flux:heading>
    </div>

    <div class="flex flex-1 items-center justify-center">
        <flux:card class="w-full max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <flux:field>
                <flux:label>{{ __('housing.apartment_name') }}</flux:label>
                <flux:input wire:model="name" type="text" required />
                <flux:error name="name" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('housing.address') }}</flux:label>
                <flux:input wire:model="address" type="text" />
                <flux:error name="address" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('housing.notes') }}</flux:label>
                <flux:textarea wire:model="notes" rows="4" />
                <flux:error name="notes" />
            </flux:field>
            <div class="flex gap-2">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    {{ __('common.save') }}
                </flux:button>
                <flux:button type="button" variant="ghost" :href="route('housing.index')" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
    </div>
</div>
