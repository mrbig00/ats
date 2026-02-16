<?php

namespace App\Actions\Positions;

use App\Models\Position;
use App\Repositories\PositionRepository;

class DeletePositionAction
{
    public function __construct(
        private PositionRepository $positionRepository,
    ) {}

    public function handle(Position $position): void
    {
        $this->positionRepository->delete($position);
    }
}
