<?php

namespace App\Policies;

use App\Enums\ExpenseStatus;
use App\Models\ChartOfAccount;
use App\Models\Expense;
use App\Models\User;

class ExpensePolicy {
    public function viewAny(User $user): bool {
        return ChartOfAccount::count() > 1;
    }

    public function view(User $user, Expense $expense): bool {
        return $user->isManager() || $expense->submitted_by === $user->id;
    }

    public function create(User $user): bool {
        return true;
    }

    public function update(User $user, Expense $expense): bool {
        if ($user->isStaff()) {
            return $expense->status === ExpenseStatus::Pending && $expense->submitted_by === $user->id;
        }
        return $user->isManager() && $expense->status === ExpenseStatus::Pending;
    }

    public function verify(User $user, Expense $expense): bool {
        return $user->isManager() && $expense->status === ExpenseStatus::Pending;
    }

    public function delete(User $user, Expense $expense): bool {
        return ($user->isManager() || $expense->submitted_by === $user->id) && $expense->status === ExpenseStatus::Pending;
    }
}