<?php

namespace App\Filament\Resources\JournalEntries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Auth;

class JournalEntryForm
{
    public static function getJournal() {
        return Step::make('Header Jurnal')
            ->icon('heroicon-m-document-text')
            ->description('Isi informasi dasar dokumen jurnal.')
            ->columnSpanFull()
            ->schema([
                Section::make([
                    DatePicker::make('date')
                        ->label('Tanggal Transaksi')
                        ->default(now())
                        ->required()
                        ->native(false),
                    Textarea::make('description')
                        ->label('Keterangan Jurnal')
                        ->placeholder('Contoh: Penyesuaian biaya penyusutan aset...')
                        ->required()
                        ->columnSpanFull(),
                ]),
                
                Hidden::make('created_by')->default(fn () => Auth::user()->id),
            ]);
    }

    public static function getItem() {
        return Step::make('Item Jurnal')
            ->icon('heroicon-m-arrows-right-left')
            ->description('Input rincian debit dan kredit akun.')
            ->schema([
                Repeater::make('items')
                    ->relationship()
                    ->hiddenLabel()
                    ->schema([
                        Select::make('coa_id')
                            ->label('Akun CoA')
                            ->relationship('coa', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('debit')
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->required(),
                        TextInput::make('credit')
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->required(),
                    ])
                    ->columns(4)
                    ->defaultItems(2)
                    ->addActionLabel('Tambah Baris Akun')
                    // Validation ERP: Balance
                    ->rules([
                        fn () => function (string $attribute, $value, \Closure $fail) {
                            $totalDebit = collect($value)->sum('debit');
                            $totalCredit = collect($value)->sum('credit');

                            if ($totalDebit !== $totalCredit) {
                                $diff = number_format(abs($totalDebit - $totalCredit), 0, ',', '.');
                                $fail("Jurnal tidak seimbang! Terdapat selisih Rp {$diff}.");
                            }
                            
                            if ($totalDebit <= 0) {
                                $fail("Nominal jurnal tidak boleh kosong.");
                            }
                        },
                    ]),
                ]);
    }
}