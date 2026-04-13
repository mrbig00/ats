<?php

namespace App\Data\Candidates;

use App\Enums\GermanLanguageLevel;
use Carbon\CarbonImmutable;

readonly class CandidateData
{
    public function __construct(
        public int $personId,
        public int $positionId,
        public int $pipelineStageId,
        public ?string $source,
        public ?CarbonImmutable $appliedAt,
        public ?string $nationality = null,
        public ?string $drivingLicenseCategory = null,
        public ?bool $hasOwnCar = null,
        public ?GermanLanguageLevel $germanLevel = null,
        public ?CarbonImmutable $availableFrom = null,
        public ?bool $housingNeeded = null,
    ) {}
}
