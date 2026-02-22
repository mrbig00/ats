<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">{{ __('user.index') }}</flux:heading>
        @can('create', \App\Models\User::class)
            <flux:button icon="plus" :href="route('settings.users.create')" wire:navigate variant="primary">
                {{ __('user.create') }}
            </flux:button>
        @endcan
    </div>

    <div wire:loading.class="opacity-50 pointer-events-none" class="relative">
        <flux:table :paginate="$this->users">
            <thead data-flux-columns>
                <tr>
                    <flux:table.column>{{ __('user.name') }}</flux:table.column>
                    <flux:table.column>{{ __('user.email') }}</flux:table.column>
                    <flux:table.column>{{ __('user.role') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </tr>
            </thead>
            <flux:table.rows>
                @forelse ($this->users as $user)
                    <flux:table.row :key="$user->id">
                        <flux:table.cell>
                            <flux:link :href="route('settings.users.edit', $user)" wire:navigate class="font-medium">
                                {{ $user->name }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>{{ $user->email }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" :color="$user->role === \App\Enums\Role::Admin ? 'red' : ($user->role === \App\Enums\Role::Hr ? 'blue' : 'zinc')" inset="top bottom">
                                {{ $user->role->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            @can('update', $user)
                                <flux:button size="sm" icon="pencil" :href="route('settings.users.edit', $user)" wire:navigate variant="ghost" inset="top bottom">
                                    {{ __('common.edit') }}
                                </flux:button>
                            @endcan
                            @can('delete', $user)
                                <flux:button size="sm" icon="trash" wire:click="deleteUser({{ $user->id }})" wire:confirm="{{ __('user.confirm_delete') }}" variant="ghost" inset="top bottom" class="text-red-600 dark:text-red-400">
                                    {{ __('common.delete') }}
                                </flux:button>
                            @endcan
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="py-8 text-center">
                            <flux:callout variant="secondary" icon="users">
                                <flux:callout.heading>{{ __('user.no_users') }}</flux:callout.heading>
                            </flux:callout>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
