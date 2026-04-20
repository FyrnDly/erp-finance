<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Traits\BarOptions;
use App\Traits\WidgetQuery;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ExpenseChart extends ChartWidget {
    use WidgetQuery,  BarOptions;

    protected string $color = 'gray';
    protected static ?int $sort = 4;
    
    protected ?string $maxHeight = '300px';
    protected ?string $pollingInterval = null;
    protected int|string|array $columnSpan = [
        'lg' => 2, 'xl' => 3
    ];

    public static function canView(): bool {
        /** @var User $user */
        $user = Auth::user();
        return $user->isManager();
    }

    protected ?string $heading = 'Distribusi Pengeluaran 6 Bulan Terakhir';
    protected ?string $description = "Analisis persentase alokasi dana untuk setiap kategori beban biaya guna mengontrol efisiensi operasional.";

    protected function getData(): array {
        return $this->getChartExpenseData();
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
