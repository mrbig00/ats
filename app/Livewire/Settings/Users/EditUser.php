<?php

namespace App\Livewire\Settings\Users;

use App\Actions\User\UpdateUserAction;
use App\Data\User\UpdateUserData;
use App\Enums\Role;
use App\Models\User;
use Livewire\Component;

class EditUser extends Component
{
    public User $user;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = 'viewer';

    public function mount(User $user): void
    {
        $this->authorize('update', $user);
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->value;
    }

    public function save(): mixed
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user->id],
            'role' => ['required', 'string', 'in:admin,hr,viewer'],
        ];

        if ($this->password !== '' || $this->password_confirmation !== '') {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        $validated = $this->validate($rules, [], [
            'name' => __('user.name'),
            'email' => __('user.email'),
            'password' => __('user.password'),
            'role' => __('user.role'),
        ]);

        $data = new UpdateUserData(
            name: $validated['name'],
            email: $validated['email'],
            role: Role::from($validated['role']),
            password: isset($validated['password']) && $validated['password'] !== '' ? $validated['password'] : null,
        );

        app(UpdateUserAction::class)->handle($this->user, $data);

        $this->dispatch('notify', __('user.updated'));

        return $this->redirect(route('settings.users.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.settings.users.edit-user')
            ->layout('layouts.app')
            ->title(__('user.edit'));
    }
}
