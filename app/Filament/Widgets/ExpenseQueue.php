<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\User;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ExpenseQueue extends TableWidget
{
    protected static ?int $sort = 5;
    protected ?string $maxHeight = '300px';
    protected static ?string $heading = "Antrean Pengajuan Biaya";
    protected ?string $description = "Daftar pengeluaran yang diajukan oleh staf dan menunggu persetujuan Anda.";

    public static function canView(): bool {
        /** @var User $user */
        $user = Auth::user();
        return $user->isManager();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Expense::query()->where('status', 'pending')->latest())
            ->columns([
                Stack::make([
                    Grid::make(2)->schema([
                        TextColumn::make('code')
                            ->color('warning')
                            ->badge(),
                        TextColumn::make('amount')
                            ->money('IDR')
                            ->alignEnd()
                            ->weight('bold'),
                    ]),
                    
                    Grid::make(2)->schema([
                        TextColumn::make('coa.name')
                            ->icon('heroicon-m-tag')
                            ->color('gray')
                            ->size('sm'),
                        TextColumn::make('submit.name')
                            ->icon('heroicon-m-user')
                            ->color('gray')
                            ->size('sm'),
                    ]),
                    
                    TextColumn::make('description')
                        ->limit(50)
                        ->color('gray')
                        ->size('sm'),
                        
                    TextColumn::make('date')
                        ->date('d M Y')
                        ->color('gray')
                        ->size('xs'),
                ])->space(3),
            ])
            ->paginated(false) 
            ->striped()
            ->modifyQueryUsing(fn (Builder $query) => $query->limit(5));
    }
}