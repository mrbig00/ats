<?php

namespace App\Actions\Positions;

use App\Data\Positions\PositionData;
use App\Models\Position;
use App\Repositories\PositionRepository;

class CreatePositionAction
{
    public function __construct(
        private PositionRepository $positionRepository,
    ) {}

    public function handle(PositionData $data): Position
    {
        return $this->positionRepository->create($data);
    }
}
