<?php

namespace App\Repositories;

use App\Models\ChatMessage;
use Illuminate\Database\Eloquent\Collection;

class ChatMessageRepository
{
    /**
     * @return Collection<int, ChatMessage>
     */
    public function getRecent(int $limit = 50): Collection
    {
        return ChatMessage::query()
            ->with('user')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    public function create(int $userId, string $body): ChatMessage
    {
        return ChatMessage::query()->create([
            'user_id' => $userId,
            'body' => $body,
        ]);
    }
}
