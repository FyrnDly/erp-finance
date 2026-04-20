<?php

namespace App\Filament\Widgets\Staff;

use App\Models\Invoice;
use App\Models\User;
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
                Stack::make([
                    Grid::make(2)->schema([
                        TextColumn::make('issue_date')->date('d M Y'),
                        TextColumn::make('due_date')->date('d M Y'),
                    ]),
                    TextColumn::make('code')
                        ->color('info')
                        ->badge(),
                    TextColumn::make('coa.name')
                        ->description(fn ($record) => $record->subject)
                        ->weight('bold'),
                    TextColumn::make('status')->badge(),
                ]),
            ])
            ->paginated(false)
            ->recordClasses(fn () => 'limit-height')
            ->striped()
            ->modifyQueryUsing(fn (Builder $query) => $query->limit(5));
    }
}