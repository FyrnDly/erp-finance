<?php

namespace App\Filament\Resources\JournalEntries\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class JournalEntriesTable
{
    public static function configure(Table $table): Table {
        return $table->columns([
            Grid::make(3)->schema([
                Stack::make([
                    TextColumn::make('code')
                        ->weight('bold')
                        ->searchable()
                        ->badge(),
                    TextColumn::make('date')
                        ->date('d M Y')
                        ->description(fn ($record) => $record->user->name ?? 'System')
                        ->sortable(),
                ]),
                
                TextColumn::make('balance.total')
                    ->money('IDR')
                    ->weight('bold')
                    ->color(fn($record) => $record->balance->is_balanced ? 'success' : 'danger')
                    ->description(fn($record) => $record->balance->status),
                
                TextColumn::make('description')
                    ->limit(150)
                    ->color('gray'),
            ]),
        ])
        ->defaultSort('created_at', 'desc') 
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
}