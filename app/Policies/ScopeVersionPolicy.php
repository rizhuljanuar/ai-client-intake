<?php

namespace App\Policies;

use App\Enums\ScopeVersionStatus;
use App\Models\ScopeVersion;
use App\Models\User;

class ScopeVersionPolicy
{
    public function view(User $user, ScopeVersion $version): bool
    {
        return $version->submission()
            ->whereHas('reviewers', fn ($query) => $query->whereKey($user->getKey()))
            ->exists();
    }

    public function decide(User $user, ScopeVersion $version): bool
    {
        return $this->view($user, $version)
            && $version->status === ScopeVersionStatus::ReadyForApproval;
    }
}
