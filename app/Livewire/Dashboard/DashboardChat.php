<?php

namespace App\Livewire\Dashboard;

use App\Actions\Chat\GetRecentChatMessagesAction;
use App\Actions\Chat\SendChatMessageAction;
use Illuminate\Support\Collection;
use Livewire\Component;

class DashboardChat extends Component
{
    public string $body = '';

    public function mount(): void
    {
        $this->authorize('viewAny', \App\Models\CalendarEvent::class);
    }

    /**
     * @return Collection<int, \App\Models\ChatMessage>
     */
    public function getMessages(): Collection
    {
        return app(GetRecentChatMessagesAction::class)->handle(50);
    }

    public function sendMessage(): void
    {
        $this->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $user = auth()->user();
        if (! $user) {
            return;
        }

        app(SendChatMessageAction::class)->handle($user->id, trim($this->body));
        $this->body = '';
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-chat', [
            'messages' => $this->getMessages(),
        ]);
    }
}
