<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\User;
use Filament\Actions\Action;
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
        return $table->query(fn (): Builder => Expense::query()->with(['coa', 'submit'])->where('status', 'pending')->latest())
        ->columns([
            Grid::make(2)->schema([
                Stack::make([
                    TextColumn::make('code')
                        ->color('warning')
                        ->badge(),
                    TextColumn::make('coa.name')
                        ->description(fn($record) => $record->submit->name),
                ]),
                Stack::make([
                    TextColumn::make('date')
                        ->label('Tanggal Nota')
                        ->alignEnd()
                        ->date(),
                    TextColumn::make('amount')
                        ->money('IDR')
                        ->alignEnd()
                        ->weight('bold')
                ])
            ]),
        ])
        ->recordAction('view')
        ->recordActions([
            Action::make('view')
                ->hiddenLabel()
                ->action(fn ($record) => redirect()->route('filament.admin.resources.expenses.view', ['record' => $record]))
        ])
        ->paginated(false) 
        ->striped()
        ->modifyQueryUsing(fn (Builder $query) => $query->limit(3));
    }
}