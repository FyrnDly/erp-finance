<?php

namespace App\Traits;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\JournalItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait WidgetQuery {
    private function getStatDocument(string $type, string $status = 'pending'): string {
        $query = $type === 'expense' ? Expense::query() : Invoice::query();
        $query->where('status', $status);

        /** @var User $user */
        $user = Auth::user();
        if ($user->isStaff()) {
            $query->where('submitted_by', $user->id);
        }

        return (string) $query->count();
    }

    private function getStatType(string $type, ?int $days = null): string {
        $formula = in_array($type, ['asset', 'expense']) 
            ? 'SUM(debit - credit)' 
            : 'SUM(credit - debit)';

        $stat = JournalItem::join('chart_of_accounts', 'journal_items.coa_id', '=', 'chart_of_accounts.id')
            ->when($days, function ($query, $value) {
                $startDate = now()->subDays($value - 1)->startOfDay();
                $endDate = now()->endOfDay();
                $query->join('journal_entries', 'journal_items.entry_id', '=', 'journal_entries.id')
                    ->whereBetween('journal_entries.date', [$startDate, $endDate]);
            })
            ->where('chart_of_accounts.type', $type)
            ->selectRaw("{$formula} as balance")
            ->value('balance') ?? 0;

        return 'Rp. ' . number_format((float) $stat, 2, ',', '.');
    }

    private function getTrendType(string $type, int $days = 7): array {
        $startDate = now()->subDays($days - 1)->startOfDay();
        $endDate = now()->endOfDay();

        $query = JournalItem::join('journal_entries', 'journal_items.entry_id', '=', 'journal_entries.id')
            ->join('chart_of_accounts', 'journal_items.coa_id', '=', 'chart_of_accounts.id')
            ->where('chart_of_accounts.type', $type)
            ->whereBetween('journal_entries.date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(journal_entries.date) as formatted_date'),
                // Formula Asset and Expense: Debit - Kredit
                DB::raw(in_array($type, ['asset', 'expense']) 
                    ? 'SUM(journal_items.debit - journal_items.credit) as balance' 
                    : 'SUM(journal_items.credit - journal_items.debit) as balance')
            )
            ->groupBy('formatted_date')
            ->get();

        $trendData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $dateStr = now()->subDays($i)->format('Y-m-d');
            
            $match = $query->firstWhere('formatted_date', $dateStr);
            $trendData[] = $match ? (int) $match->balance : 0;
        }

        return $trendData;
    }

    public function getChartData(string $type, int $month = 6) {
        $startDate = now()->subMonths($month - 1)->startOfMonth();
        $endDate = now()->endOfMonth();

        $query = JournalItem::join('journal_entries', 'journal_items.entry_id', '=', 'journal_entries.id')
            ->join('chart_of_accounts', 'journal_items.coa_id', '=', 'chart_of_accounts.id')
            ->where('chart_of_accounts.type', $type)
            ->whereBetween('journal_entries.date', [$startDate, $endDate])
            ->select(
                DB::raw("EXTRACT(YEAR FROM journal_entries.date)::int as year"),
                DB::raw("EXTRACT(MONTH FROM journal_entries.date)::int as month"),

                DB::raw($type === 'expense' || $type === 'asset' 
                    ? 'SUM(journal_items.debit - journal_items.credit) as balance' 
                    : 'SUM(journal_items.credit - journal_items.debit) as balance')
            )
            ->groupBy('year', 'month')
            ->get();

        $labels = [];
        $data = [];

        for ($i = $month - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');

            $match = $query->firstWhere(function ($item) use ($date) {
                return $item->year == $date->year && $item->month == $date->month;
            });

            $data[] = $match ? (float) $match->balance : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
    
    public function getChartExpenseData(string $status = 'approved', int $month = 6) {
        $startDate = now()->subMonths($month - 1)->startOfMonth();
        $endDate = now()->endOfMonth();

        $query = Expense::join('chart_of_accounts', 'expenses.coa_id', '=', 'chart_of_accounts.id')
            ->whereBetween('expenses.date', [$startDate, $endDate])
            ->where('expenses.status', $status)
            ->select(
                'chart_of_accounts.name',
                DB::raw("EXTRACT(YEAR FROM expenses.date)::int as year"),
                DB::raw("EXTRACT(MONTH FROM expenses.date)::int as month"),
                DB::raw('SUM(expenses.amount) AS total')
            )
            ->groupBy('year', 'month', 'chart_of_accounts.name')
            ->get();

        $labels = [];
        $monthReferences = [];
        
        for ($i = $month - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');
            $monthReferences[] = [
                'year' => $date->year,
                'month' => $date->month
            ];
        }

        $groupedByCoa = $query->groupBy('name');
        $datasets = [];
        
        $colors = ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899', '#14b8a6'];
        $colorIndex = 0;

        foreach ($groupedByCoa as $coaName => $items) {
            $dataArray = [];

            foreach ($monthReferences as $ref) {
                $match = $items->firstWhere(function ($item) use ($ref) {
                    return $item->year == $ref['year'] && $item->month == $ref['month'];
                });

                $dataArray[] = $match ? (float) $match->total : 0; 
            }

            $currentColor = $colors[$colorIndex % count($colors)];
            $colorIndex++;

            $datasets[] = [
                'label' => $coaName,
                'data' => $dataArray,
                'borderColor' => $currentColor,
                'backgroundColor' => $currentColor . '33',
                'fill' => 'start',
                'tension' => 0.4,
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }
}
