<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeTerminated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $employeeId,
        public int $personId,
        public string $status,
        public ?string $exitDate,
    ) {}
}
