<?php

namespace App\Policies;

use App\Models\ChartOfAccount;
use App\Models\User;

class ChartOfAccountPolicy {
    public function viewAny(User $user): bool {
        if (ChartOfAccount::count() <= 1) {
            return false;
        }
        return $user->isManager();
    }

    public function view(User $user, ChartOfAccount $chartOfAccount): bool {
        return $user->isManager();
    }

    public function create(User $user): bool {
        return $user->isManager();
    }

    public function update(User $user, ChartOfAccount $chartOfAccount): bool {
        return $user->isManager();
    }

    public function delete(User $user, ChartOfAccount $chartOfAccount): bool {
        return $user->isManager();
    }
}