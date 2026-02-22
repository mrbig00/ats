<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center gap-2">
        <flux:button variant="ghost" icon="arrow-left" :href="route('settings.users.index')" wire:navigate>
            {{ __('common.back') }}
        </flux:button>
        <flux:heading size="xl" level="1">{{ __('user.edit') }}</flux:heading>
    </div>

    <div class="flex flex-1 items-center justify-center">
        <flux:card class="w-full max-w-2xl">
            <form wire:submit="save" class="space-y-6">
                <flux:field>
                    <flux:label>{{ __('user.name') }}</flux:label>
                    <flux:input wire:model="name" type="text" required />
                    <flux:error name="name" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('user.email') }}</flux:label>
                    <flux:input wire:model="email" type="email" required />
                    <flux:error name="email" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('user.password') }}</flux:label>
                    <flux:input wire:model="password" type="password" :placeholder="__('user.password_leave_blank')" />
                    <flux:error name="password" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('user.password_confirmation') }}</flux:label>
                    <flux:input wire:model="password_confirmation" type="password" :placeholder="__('user.password_leave_blank')" />
                    <flux:error name="password_confirmation" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('user.role') }}</flux:label>
                    <flux:select wire:model="role" class="w-full">
                        <flux:select.option value="admin">{{ __('user.role_admin') }}</flux:select.option>
                        <flux:select.option value="hr">{{ __('user.role_hr') }}</flux:select.option>
                        <flux:select.option value="viewer">{{ __('user.role_viewer') }}</flux:select.option>
                    </flux:select>
                    <flux:error name="role" />
                </flux:field>
                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        {{ __('common.save') }}
                    </flux:button>
                    <flux:button type="button" variant="ghost" :href="route('settings.users.index')" wire:navigate>
                        {{ __('common.cancel') }}
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</div>
