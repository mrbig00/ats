<?php

namespace App\Data\Positions;

use App\Enums\PositionUrgency;
use Carbon\CarbonImmutable;

readonly class PositionData
{
    public function __construct(
        public string $title,
        public ?string $description,
        public string $status,
        public ?PositionUrgency $urgency,
        public ?CarbonImmutable $opensAt,
        public ?CarbonImmutable $closesAt,
    ) {}
}
