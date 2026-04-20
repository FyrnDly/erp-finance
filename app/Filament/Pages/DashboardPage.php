<?php

namespace App\Filament\Pages;

use App\Models\ChartOfAccount;
use App\Models\User;
use Filament\Pages\Dashboard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class DashboardPage extends Dashboard {
    public static function canAccess(): bool {
        return ChartOfAccount::count() > 1;
    }

    public function getColumns(): int|array {
        /** @var User $user */
        $user = Auth::user();
        if ($user->isManager()) {
            return [
                'lg' => 2, 'xl' => 4
            ];
        }

        return ['lg' => 3];
    }

    public function getHeading(): string|Htmlable|null {
        /** @var User $user */
        $user = Auth::user();
        return "Halo {$user->name}, Selamat Datang!";
    }
}
