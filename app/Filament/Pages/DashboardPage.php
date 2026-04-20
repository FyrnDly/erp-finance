<?php

namespace App\Filament\Pages;

use App\Models\ChartOfAccount;
use Filament\Pages\Dashboard;

class DashboardPage extends Dashboard {
    public static function canAccess(): bool {
        return ChartOfAccount::count() > 1;
    }
}
