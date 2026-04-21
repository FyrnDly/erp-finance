<?php

namespace App\Filament\Widgets\Staff;

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

class InvoiceTable extends TableWidget {
    protected static ?int $sort = 2;
    protected ?string $maxHeight = '300px';
    protected int|string|array $columnSpan = 'full';
    
    protected static ?string $heading = "Daftar Invoice"; 
    protected ?string $description = "Daftar invoice yang telah Anda ajukan kepada Manager.";

    public static function canView(): bool {
        /** @var User $user */
        $user = Auth::user();
        return $user->isStaff();
    }

    public function table(Table $table): Table {
        return $table
            ->query(fn (): Builder => Invoice::query()->where('submitted_by', Auth::id())->latest())
            ->columns([
                Grid::make(3)->schema([
                    Stack::make([
                        TextColumn::make('code')
                            ->badge(),
                        TextColumn::make('coa.name'),
                        TextColumn::make('issue_date')
                            ->label('Tgl. Terbit')
                            ->date(),
                    ]),
                    
                    Stack::make([
                        TextColumn::make('status')->badge(),
                        TextColumn::make('subject')->weight('bold'),
                        TextColumn::make('approve.name')->visible(fn ($state) => $state),
                    ]),
                    
                    Stack::make([
                        TextColumn::make('grand_total')
                            ->label('Total')
                            ->getStateUsing(fn ($record) => $record->items->sum('total'))
                            ->money('IDR')
                            ->weight('bold')
                            ->color('success'),
                        TextColumn::make('due_date')
                            ->date('d M Y')
                            ->color('danger')
                            ->size('xs')
                            ->prefix('Jatuh tempo: '),
                    ])->alignEnd(),
                ])
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