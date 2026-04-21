<?php

namespace App\Filament\Resources\ChartOfAccounts;

use App\Enums\CoaType;
use App\Filament\Resources\ChartOfAccounts\Pages\ManageChartOfAccounts;
use App\Models\ChartOfAccount;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ChartOfAccountResource extends Resource
{
    protected static ?string $model = ChartOfAccount::class;

    protected static string|UnitEnum|null $navigationGroup = "Buku Besar";
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $label = "Daftar Akun (CoA)";
    protected static ?string $recordTitleAttribute = 'name';
    
    public static function form(Schema $schema): Schema {
        return $schema->components([
            TextInput::make('name')
                ->label("Nama Akun (CoA)")
                ->placeholder("Contoh: Kas Utama, Biaya Listrik, Pajak")
                ->helperText("Gunakan nama yang jelas dan spesifik agar mudah diidentifikasi saat penjurnalan.")
                ->required()
                ->unique(ignoreRecord: true),
                
            Select::make('type')
                ->label("Kategori Tipe Akun")
                ->options(CoaType::class)
                ->helperText("Tentukan posisi akun ini di laporan keuangan. Contoh: Aset (Harta), Expense (Beban/Biaya).")
                ->required()
                ->native(false),
                
            Textarea::make('description')
                ->label("Deskripsi Singkat")
                ->placeholder("Contoh: Akun ini digunakan untuk mencatat pengeluaran listrik bulanan kantor pusat.")
                ->helperText("Opsional: Berikan catatan untuk membantu staf memahami kegunaan akun ini.")
                ->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name')
                ->placeholder('-')
                ->color('gray')
                ->badge(),
            TextEntry::make('name')
                ->label("Nama CoA"),
            TextEntry::make('type')
                ->badge(),
            TextEntry::make('description')
                ->placeholder('-')
                ->columnSpanFull(),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->columns([
                Grid::make(2)->schema([
                    Stack::make([
                        TextColumn::make('type')
                            ->badge()
                            ->searchable(),
                        TextColumn::make('name')
                            ->description(fn ($record) => $record->code)
                            ->searchable(),
                    ]),
                    TextColumn::make('description')
                        ->limit(150)
                ]),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageChartOfAccounts::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
