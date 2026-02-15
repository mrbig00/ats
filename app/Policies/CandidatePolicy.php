<?php

namespace App\Policies;

use App\Models\Candidate;
use App\Models\User;

class CandidatePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Candidate $candidate): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Candidate $candidate): bool
    {
        return true;
    }

    public function delete(User $user, Candidate $candidate): bool
    {
        return false;
    }

    public function convertToEmployee(User $user, Candidate $candidate): bool
    {
        return true;
    }
}
