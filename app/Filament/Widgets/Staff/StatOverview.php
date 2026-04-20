<?php

namespace App\Filament\Widgets\Staff;

use App\Models\User;
use App\Traits\WidgetQuery;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatOverview extends StatsOverviewWidget {
    use WidgetQuery;
    protected ?string $heading = 'Ikhtisar Keuangan Perusahaan';
    protected ?string $description = 'Ringkasan indikator keuangan utama untuk memantau likuiditas dan kinerja operasional bulan ini.';
    
    protected static ?int $sort = 1;
    protected ?string $pollingInterval = null;

    public static function canView(): bool {
        /** @var User $user */
        $user = Auth::user();
        return $user->isStaff();
    }

    protected function getStats(): array {
        return [
            Stat::make('Invoice Belum Lunas', $this->getStatDocument('invoice', 'unpaid'))
                ->description("Tagihan klien yang telah diterbitkan namun belum dibayar.")
                ->descriptionIcon(Heroicon::OutlinedDocument, 'before')
                ->color('info'),
            Stat::make('Pengeluran Menunggu', $this->getStatDocument('expense'))
                ->description("Klaim biaya Anda yang sedang menunggu persetujuan Manager.")
                ->descriptionIcon(Heroicon::OutlinedClock, 'before')
                ->color('warning'),
            Stat::make('Pengeluran Ditolak', $this->getStatDocument('expense', 'rejected'))
                ->description("Klaim biaya yang dikembalikan oleh Manager karena ada kesalahan.")
                ->descriptionIcon(Heroicon::XCircle, 'before')
                ->color('danger')
            
        ];
    }
}
