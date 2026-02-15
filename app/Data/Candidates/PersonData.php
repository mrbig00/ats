<?php

namespace App\Data\Candidates;

readonly class PersonData
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public ?string $email,
        public ?string $phone,
    ) {}
}
