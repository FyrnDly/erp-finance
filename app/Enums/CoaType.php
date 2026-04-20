<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CoaType: string implements HasLabel, HasColor {
    case Asset = 'asset';
    case Liability = 'liability';
    case Equity = 'equity';
    case Revenue = 'revenue';
    case Expense = 'expense';

    public function getLabel(): string {
        return match($this) {
            self::Asset => 'Aktiva (Aset)',
            self::Liability => 'Kewajiban (Hutang)',
            self::Equity => 'Ekuitas (Modal)',
            self::Revenue => 'Pendapatan',
            self::Expense => 'Beban Biaya',
        };
    }

    public function getColor(): string|array|null {
        return match ($this) {
            self::Asset => 'info',
            self::Liability => 'warning',
            self::Equity => 'gray',
            self::Revenue => 'success',
            self::Expense => 'danger',
        };
    }
}
