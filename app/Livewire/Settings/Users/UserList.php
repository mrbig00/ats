<?php

namespace App\Livewire\Settings\Users;

use App\Actions\User\DeleteUserAction;
use App\Actions\User\ListUsersAction;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public int $perPage = 15;

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<User>
     */
    public function getUsersProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return app(ListUsersAction::class)->handle($this->perPage);
    }

    public function deleteUser(int $userId): mixed
    {
        $user = User::query()->findOrFail($userId);
        $this->authorize('delete', $user);

        app(DeleteUserAction::class)->handle($user);

        $this->dispatch('notify', __('user.deleted'));

        return $this->redirect(route('settings.users.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.settings.users.user-list')
            ->layout('layouts.app')
            ->title(__('user.index'));
    }
}
