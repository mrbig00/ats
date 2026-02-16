<?php

namespace App\Data\Housing;

readonly class RoomData
{
    public function __construct(
        public int $apartmentId,
        public string $name,
        public ?string $notes,
    ) {}
}
