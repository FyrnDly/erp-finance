<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseInfolist
{
    public static function configure(Schema $schema): Schema {
        return $schema->components([
            Section::make('Dokumen Pengeluaran')->schema([
                TextEntry::make('coa.name')
                    ->label('Kategori Akun CoA')
                    ->color(fn ($record) => $record->coa->type->getColor())
                    ->badge()
                    ->weight('bold'),
                TextEntry::make('date')
                    ->label('Tanggal Nota')
                    ->date(),
                TextEntry::make('status')->badge(),
                
                TextEntry::make('amount')
                    ->label('Total')
                    ->money('IDR')
                    ->weight('bold')
                    ->size('lg'),
                TextEntry::make('description')
                    ->label('Deskripsi Pengajuan')
                    ->columnSpanFull(),
            ])->columns([
                'md' => 2, 'xl' => 3
            ]),
                
            Section::make('Informasi Verifikasi')
                ->schema([
                    TextEntry::make('submit.name')
                        ->label('Diajukan oleh'),
                    TextEntry::make('approve.name')
                        ->default("Menunggu Verifikasi Manager")
                        ->label('Diverifikasi oleh'),
                    TextEntry::make('approved_date')
                        ->visible(fn ($record) => $record->status->value !== 'pending')
                        ->label('Waktu Verifikasi')
                        ->date(),
                    TextEntry::make('refusal')
                        ->label('Alasan Penolakan')
                        ->visible(fn ($record) => $record->status->value == 'rejected')
                        ->columnSpanFull(),
                ])->columns(['md' => 3]),
        ]);
    }
}
