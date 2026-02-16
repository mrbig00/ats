<flux:modal wire:model="show" class="sm:max-w-md">
    <form wire:submit="endOccupancy">
        <flux:heading size="lg">{{ __('housing.end_occupancy') }}</flux:heading>
        <flux:subheading class="mt-1">{{ __('housing.end_occupancy_confirm') }}</flux:subheading>
        <div class="mt-4">
            <flux:field>
                <flux:label>{{ __('housing.occupancy_ends_at') }}</flux:label>
                <flux:input wire:model="endsAt" type="date" required />
                <flux:error name="endsAt" />
            </flux:field>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <flux:button type="button" variant="ghost" wire:click="close">
                {{ __('common.cancel') }}
            </flux:button>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                {{ __('housing.end_occupancy') }}
            </flux:button>
        </div>
    </form>
</flux:modal>
