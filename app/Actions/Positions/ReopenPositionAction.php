<?php

namespace App\Actions\Positions;

use App\Models\Position;
use App\Repositories\PositionRepository;

class ReopenPositionAction
{
    public function __construct(
        private PositionRepository $positionRepository,
    ) {}

    public function handle(Position $position): Position
    {
        if (! $position->hasExpiredRecruitmentSession()) {
            throw new \InvalidArgumentException('Position is not in an expired recruitment session.');
        }

        return $this->positionRepository->reopenAfterExpiredSession($position);
    }
}
