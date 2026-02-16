<?php

namespace App\Data\Housing;

readonly class ApartmentData
{
    public function __construct(
        public string $name,
        public ?string $address,
        public ?string $notes,
    ) {}
}
