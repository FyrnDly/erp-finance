<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Traits\BarOptions;
use App\Traits\WidgetQuery;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class CashFlowChart extends ChartWidget {
    use WidgetQuery,  BarOptions;

    protected string $color = 'gray';
    protected static ?int $sort = 2;
    
    protected int|string|array $columnSpan = [
        'lg' => 2, 'xl' => 3
    ];
    protected ?string $pollingInterval = null;
    protected ?string $maxHeight = '300px';

    protected ?string $heading = 'Cash Flow 6 Bulan Terakhir';
    protected ?string $description = "Perbandingan visual antara total pendapatan dan pengeluaran setiap bulan untuk memantau profitabilitas perusahaan.";

    public static function canView(): bool {
        /** @var User $user */
        $user = Auth::user();
        return $user->isManager();
    }

    protected function getData(): array {
        $revenue = $this->getChartData('revenue');
        $expense = $this->getChartData('expense');

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Revenue)',
                    'data' => $revenue['data'],
                    'borderColor' => '#10b981',
                    'backgroundColor' => '#10b98133',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Pengeluaran (Expense)',
                    'data' => $expense['data'],
                    'borderColor' => '#ef4444',
                    'backgroundColor' => '#ef444433',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $revenue['labels'], 
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
