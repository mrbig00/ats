<?php

namespace App\Actions\Chat;

use App\Repositories\ChatMessageRepository;
use Illuminate\Database\Eloquent\Collection;

class GetRecentChatMessagesAction
{
    public function __construct(
        private ChatMessageRepository $chatMessageRepository,
    ) {}

    /**
     * @return Collection<int, \App\Models\ChatMessage>
     */
    public function handle(int $limit = 50): Collection
    {
        return $this->chatMessageRepository->getRecent($limit);
    }
}
