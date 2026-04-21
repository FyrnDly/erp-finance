<?php

namespace App\Filament\Resources\JournalEntries\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JournalEntryInfolist {
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Jurnal')->schema([
                    TextEntry::make('code')
                        ->label('Nomor Jurnal')
                        ->weight('bold')
                        ->color('primary')
                        ->badge(),
                    TextEntry::make('date')
                        ->label('Tanggal Transaksi')
                        ->date('d F Y'),
                    TextEntry::make('user.name')
                        ->label('Dibuat Oleh')
                        ->default("System")
                        ->badge()
                        ->color('gray'),
                    TextEntry::make('created_at')
                        ->label('Waktu Posting')
                        ->dateTime(),
                    TextEntry::make('description')
                        ->label('Keterangan / Memo')
                        ->prose()
                        ->markdown()
                        ->columnSpanFull(),
                ])->columns([
                    'md' => 2,
                    'lg' => 4
                ]),

                Section::make('Rincian Pembukuan')
                    ->description('Daftar entri akun yang diposting pada jurnal ini.')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label(false)
                            ->schema([
                                Grid::make(4)->schema([
                                    TextEntry::make('coa.name')
                                        ->label('Akun')
                                        ->columnSpan(2)
                                        ->weight('medium'),
                                    TextEntry::make('debit')
                                        ->label('Debit')
                                        ->money('IDR')
                                        ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
                                    TextEntry::make('credit')
                                        ->label('Kredit')
                                        ->money('IDR')
                                        ->color(fn ($state) => $state > 0 ? 'danger' : 'gray'),
                                ]),
                            ]),
                    ]),
            ]);
    }
}