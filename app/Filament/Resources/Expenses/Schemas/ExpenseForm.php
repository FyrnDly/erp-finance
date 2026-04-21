<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Enums\CoaType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Auth;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Rincian Pengajuan Biaya')
                ->description('Lengkapi formulir pengeluaran dengan data yang valid.')
                ->columnSpanFull()
                ->schema([
                    Select::make('coa_id')
                        ->label('Kategori Beban (CoA)')
                        ->relationship('coa', 'name', fn($query) => $query->where('type', CoaType::Expense->value))
                        ->searchable()
                        ->preload()
                        ->required(),
                        
                    DatePicker::make('date')
                        ->label('Tanggal Nota/Invoice')
                        ->default(now())
                        ->required()
                        ->native(false),
                        
                    TextInput::make('amount')
                        ->label('Nominal Pengeluaran')
                        ->numeric()
                        ->prefix('Rp')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->required()
                        ->minValue(1)
                        ->columnSpanFull(),
                        
                    Textarea::make('description')
                        ->label('Tujuan / Keterangan')
                        ->placeholder('Contoh: Pembayaran tagihan listrik kantor bulan November.')
                        ->required()
                        ->columnSpanFull(),
                        
                    Hidden::make('submitted_by')->default(fn () => Auth::id()),
                ])->columns(2),
        ]);
    }
}