<?php

namespace App\Livewire\Settings\Users;

use App\Actions\User\CreateUserAction;
use App\Data\User\UserData;
use App\Enums\Role;
use App\Models\User;
use Livewire\Component;

class CreateUser extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = 'viewer';

    public function mount(): void
    {
        $this->authorize('create', User::class);
    }

    public function save(): mixed
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:admin,hr,viewer'],
        ], [], [
            'name' => __('user.name'),
            'email' => __('user.email'),
            'password' => __('user.password'),
            'role' => __('user.role'),
        ]);

        $data = new UserData(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
            role: Role::from($validated['role']),
        );

        app(CreateUserAction::class)->handle($data);

        $this->dispatch('notify', __('user.created'));

        return $this->redirect(route('settings.users.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.settings.users.create-user')
            ->layout('layouts.app')
            ->title(__('user.create'));
    }
}
