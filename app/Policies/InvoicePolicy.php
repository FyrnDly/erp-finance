<?php

namespace App\Policies;

use App\Enums\InvoiceStatus;
use App\Models\ChartOfAccount;
use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy {
    public function viewAny(User $user): bool {
        return ChartOfAccount::count() > 1;
    }

    public function view(User $user, Invoice $invoice): bool {
        return $user->isManager() || $invoice->submitted_by === $user->id;
    }

    public function create(User $user): bool {
        return true;
    }

    public function update(User $user, Invoice $invoice): bool {
        if ($user->isStaff()) {
            return $invoice->status === InvoiceStatus::Unpaid && $invoice->submitted_by === $user->id && $invoice->due_date > now();
        }
        return $user->isManager() && $invoice->status === InvoiceStatus::Unpaid;
    }

    public function pay(User $user, Invoice $invoice): bool {
        if ($invoice->due_date < now()) {
            return false;
        }
        return $user->isManager() && $invoice->status === InvoiceStatus::Unpaid;
    }

    public function delete(User $user, Invoice $invoice): bool {
        return ($user->isManager() || $invoice->submitted_by === $user->id) && $invoice->status === InvoiceStatus::Unpaid;
    }
}