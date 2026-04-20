<?php

namespace App\Filament\Widgets;

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
    protected int|array|null $columns = [
        'lg' => 2, 'xl' => 4
    ];

    public static function canView(): bool {
        /** @var User $user */
        $user = Auth::user();
        return $user->isManager();
    }

    protected function getStats(): array {
        $day = now()->day;
        $documents = $this->getStatDocument('invoice', 'unpaid') + $this->getStatDocument('expense');
        return [
            Stat::make('Total Aset', $this->getStatType('asset'))
                ->description("Saldo gabungan dari seluruh akun aset perusahaan saat ini.")
                ->descriptionIcon(Heroicon::OutlinedWallet, 'before')
                ->chart($this->getTrendType('asset', 31))
                ->color('info'),
            Stat::make('Pendapatan Terbayar', $this->getStatType('revenue', $day))
                ->description("Total nilai invoice berstatus 'Lunas' selama bulan berjalan.")
                ->descriptionIcon(Heroicon::ArrowTrendingUp, 'after')
                ->chart($this->getTrendType('revenue', $day))
                ->color('success'),
            Stat::make('Beban Operasional', $this->getStatType('expense', $day))
                ->description("Total pengeluaran yang telah disetujui (Approved) bulan ini.")
                ->descriptionIcon(Heroicon::ArrowTrendingDown, 'after')
                ->chart($this->getTrendType('expense', $day))
                ->color('warning'),
            Stat::make('Antrean Dokumen', $documents)
                ->description("Dokumen pengeluaran dan invoice yang memerlukan validasi Anda segera.")
                ->descriptionIcon(Heroicon::ExclamationTriangle, 'before')
                ->color('danger'),
        ];
    }
}
