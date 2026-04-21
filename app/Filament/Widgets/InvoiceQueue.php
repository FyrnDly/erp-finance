<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class InvoiceQueue extends TableWidget {
    protected static ?int $sort = 3;
    protected ?string $maxHeight = '300px';
    protected static ?string $heading = "Antrean Invoice";
    protected ?string $description = "Daftar invoice yang diajukan oleh staf dan memerlukan validasi segera dari Anda.";

    public static function canView(): bool {
        /** @var User $user */
        $user = Auth::user();
        return $user->isManager();
    }

    public function table(Table $table): Table {
        return $table
            ->query(fn (): Builder => Invoice::query()->where('status', 'unpaid')->latest())
            ->columns([
                Grid::make(2)->schema([
                    Stack::make([
                        TextColumn::make('code')
                            ->badge(),
                        TextColumn::make('coa.name'),
                        TextColumn::make('subject')
                            ->weight('lgiht')
                            ->size('sm')
                            ->limit(25)
                    ]),
                    
                    Stack::make([
                        TextColumn::make('due_date')
                            ->date('d M Y')
                            ->color('danger')
                            ->size('xs')
                            ->prefix('Jatuh tempo: '),
                        TextColumn::make('grand_total')
                            ->label('Total')
                            ->getStateUsing(fn ($record) => $record->items->sum('total'))
                            ->money('IDR')
                            ->weight('bold')
                            ->color('success'),
                    ])->alignEnd(),
                ]),
            ])
            ->recordAction('view')
            ->recordActions([
                Action::make('view')
                    ->hiddenLabel()
                    ->action(fn ($record) => redirect()->route('filament.admin.resources.invoices.view', ['record' => $record]))
            ])
            ->paginated(false)
            ->recordClasses(fn () => 'limit-height')
            ->striped()
            ->modifyQueryUsing(fn (Builder $query) => $query->limit(5));
    }
}