<?php

namespace App\Data\Positions;

use Carbon\CarbonImmutable;

readonly class PositionData
{
    public function __construct(
        public string $title,
        public ?string $description,
        public string $status,
        public ?CarbonImmutable $opensAt,
        public ?CarbonImmutable $closesAt,
    ) {}
}
