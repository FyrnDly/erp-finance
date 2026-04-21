<?php

namespace App\Filament\Widgets\Staff;

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

class ExpenseTable extends TableWidget
{
    protected static ?int $sort = 3;
    protected ?string $maxHeight = '300px';
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = "Pengajuan Biaya";
    protected ?string $description = "Daftar pengeluaran yang Anda ajukan dan status persetujuannya."; 

    public static function canView(): bool {
        /** @var User $user */
        $user = Auth::user();
        return $user->isStaff();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Expense::query()->where('submitted_by', Auth::id())->latest())
            ->columns([
                Grid::make(4)->schema([
                    Stack::make([
                        TextColumn::make('code')
                            ->color('warning')
                            ->badge(),
                        TextColumn::make('coa.name'),
                        TextColumn::make('date')
                            ->label('Tanggal Nota')
                            ->date(),
                    ]),
                    Stack::make([
                        TextColumn::make('status')->badge(),
                        TextColumn::make('approved_date')
                            ->label('Tanggal Persetujuan')
                            ->date()
                            ->visible(fn ($state) => $state),
                        TextColumn::make('approve.name')
                            ->visible(fn ($state) => $state),
                        TextColumn::make('refusal')
                            ->limit(150)
                            ->color('gray')
                            ->size('sm')
                            ->visible(fn ($state) => $state),
                    ]),
                    TextColumn::make('description')
                        ->limit(150)
                        ->color('gray')
                        ->size('sm'),
                    TextColumn::make('amount')
                        ->money('IDR')
                        ->alignEnd()
                        ->weight('bold'),
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
            ->modifyQueryUsing(fn (Builder $query) => $query->limit(5));
    }
}