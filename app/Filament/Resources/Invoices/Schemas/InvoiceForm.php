<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Enums\CoaType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Auth;

class InvoiceForm
{
    public static function getInvoice() {
        return Step::make('Data Tagihan')
            ->icon('heroicon-m-document-text')
            ->description('Informasi klien dan kategori pendapatan.')
            ->schema([
                TextInput::make('subject')
                    ->label('Subjek / Nama Klien')
                    ->placeholder('Contoh: Tagihan Jasa IT PT ABC')
                    ->required()
                    ->columnSpanFull(),
                
                Select::make('coa_id')
                    ->label('Kategori Pendapatan (CoA)')
                    ->relationship('coa', 'name', fn($query) => $query->where('type', CoaType::Revenue->value))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                DatePicker::make('issue_date')
                    ->label('Tanggal Terbit')
                    ->default(now())
                    ->required()
                    ->native(false),
                    
                DatePicker::make('due_date')
                    ->label('Jatuh Tempo')
                    ->default(now()->addDays(14))
                    ->required()
                    ->native(false),

                Hidden::make('submitted_by')->default(fn () => Auth::id()),
            ])->columns(2);
    }

    public static function getItem() {
        return Step::make('Rincian Item')
            ->icon('heroicon-m-list-bullet')
            ->description('Masukkan daftar barang/jasa yang ditagihkan.')
            ->schema([
                Repeater::make('items')
                    ->relationship()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Item/Jasa')
                            ->required()
                            ->columnSpan(2),
                            
                        TextInput::make('quantity')
                            ->label('Kuantitas')
                            ->numeric()
                            ->default(1)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $get, callable $set) => 
                                $set('total', $state * ((float) $get('price') ?? 0))
                            )
                            ->required(),
                            
                        TextInput::make('price')
                            ->label('Harga Satuan')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->prefix('Rp')
                            ->live(onBlur: true)
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->afterStateUpdated(fn ($state, callable $get, callable $set) => 
                                $set('total', $state * ((int) $get('quantity') ?? 1))
                            ),
                            
                        TextInput::make('total')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->readOnly()
                            ->required(),
                    ])
                    ->columns(5)
                    ->defaultItems(1)
                    ->addActionLabel('Tambah Item Tagihan'),
            ]);
    }
}