<?php

namespace App\Actions\Positions;

use App\Data\Positions\PositionData;
use App\Models\Position;
use App\Repositories\PositionRepository;

class UpdatePositionAction
{
    public function __construct(
        private PositionRepository $positionRepository,
    ) {}

    public function handle(Position $position, PositionData $data): Position
    {
        return $this->positionRepository->update($position, $data);
    }
}
