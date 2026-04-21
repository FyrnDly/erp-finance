<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum InvoiceStatus: string implements HasLabel, HasColor, HasIcon {
    case Unpaid = 'unpaid';
    case Paid = 'paid';

    public function getLabel(): ?string {
        return match ($this) {
            self::Unpaid => 'Belum Bayar',
            self::Paid => 'Lunas',
        };
    }

    public function getColor(): string|array|null {
        return match ($this) {
            self::Unpaid => 'warning',
            self::Paid => 'success',
        };
    }
    
    public function getIcon(): string|BackedEnum|Htmlable|null {
        return match ($this) {
            self::Unpaid => Heroicon::OutlinedClock,
            self::Paid => Heroicon::OutlinedCheckBadge,
        };
    }
}