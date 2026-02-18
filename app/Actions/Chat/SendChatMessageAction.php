<?php

namespace App\Actions\Chat;

use App\Models\ChatMessage;
use App\Repositories\ChatMessageRepository;

class SendChatMessageAction
{
    public function __construct(
        private ChatMessageRepository $chatMessageRepository,
    ) {}

    public function handle(int $userId, string $body): ChatMessage
    {
        return $this->chatMessageRepository->create($userId, $body);
    }
}
