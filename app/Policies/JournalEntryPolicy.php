<?php

namespace App\Policies;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\User;

class JournalEntryPolicy {
    public function viewAny(User $user): bool {
        if (ChartOfAccount::count() <= 1) {
            return false;
        }
        return $user->isManager();
    }

    public function view(User $user, JournalEntry $journalEntry): bool {
        return $user->isManager();
    }

    public function create(User $user): bool {
        return $user->isManager();
    }

    public function update(User $user, JournalEntry $journalEntry): bool {
        return $user->isManager();
    }

    public function delete(User $user, JournalEntry $journalEntry): bool {
        return $user->isManager();
    }
}
