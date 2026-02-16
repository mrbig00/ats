<?php

namespace App\Actions\Housing;

use App\Models\Room;
use App\Repositories\RoomRepository;

class DeleteRoomAction
{
    public function __construct(
        private RoomRepository $roomRepository,
    ) {}

    public function handle(Room $room): void
    {
        $this->roomRepository->delete($room);
    }
}
