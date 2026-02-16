<?php

namespace App\Actions\Housing;

use App\Data\Housing\RoomData;
use App\Models\Room;
use App\Repositories\RoomRepository;

class CreateRoomAction
{
    public function __construct(
        private RoomRepository $roomRepository,
    ) {}

    public function handle(RoomData $data): Room
    {
        return $this->roomRepository->create($data);
    }
}
