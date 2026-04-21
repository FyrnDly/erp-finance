<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Tagihan')->schema([
                TextEntry::make('coa.name')
                    ->label('Kategori Akun CoA')
                    ->color(fn ($record) => $record->coa->type->getColor())
                    ->badge()
                    ->weight('bold'),
                Grid::make(2)->schema([
                    TextEntry::make('issue_date')
                        ->label('Tgl. Terbit')
                        ->date('d M Y'),
                    TextEntry::make('due_date')
                        ->label('Jatuh Tempo')
                        ->date('d M Y')
                        ->color('danger'),
                ]),
                TextEntry::make('status')
                    ->label('Status Pembayaran')
                    ->badge(),
                TextEntry::make('subject')
                    ->label('Subjek Tagihan')
                    ->columnSpanFull()
                    ->weight('bold')
                    ->size('lg'),
            ])->columns([
                'md' => 2, 'xl' => 3
            ]),

            Section::make('Rincian Pembayaran')
                ->schema([
                    RepeatableEntry::make('items')
                        ->hiddenLabel()
                        ->label(false)
                        ->schema([
                            Grid::make(3)->schema([
                                TextEntry::make('name')
                                    ->label('Item'),
                                TextEntry::make('quantity')
                                    ->label('Qty')
                                    ->prefix('@'),
                                TextEntry::make('price')
                                    ->label('Harga')
                                    ->money('IDR'),
                            ]),
                            TextEntry::make('total')
                                ->hiddenLabel()
                                ->money('IDR')
                                ->weight('bold')
                                ->size('lg')
                                ->color('success')
                                ->columnSpanFull()
                                ->alignEnd(),
                        ]),
                ]),
        ]);
    }
}