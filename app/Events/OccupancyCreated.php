<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OccupancyCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $occupancyId,
        public int $roomId,
        public int $employeeId,
    ) {}
}
