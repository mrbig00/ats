<?php

namespace App\Actions\Housing;

use App\Data\Housing\RoomData;
use App\Models\Room;
use App\Repositories\RoomRepository;

class UpdateRoomAction
{
    public function __construct(
        private RoomRepository $roomRepository,
    ) {}

    public function handle(Room $room, RoomData $data): Room
    {
        return $this->roomRepository->update($room, $data);
    }
}
