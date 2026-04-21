<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        /** @var User $user */
        $user = Auth::user();
        return $table->columns([
            Grid::make(3)->schema([
                Stack::make([
                    TextColumn::make('code')
                        ->searchable()
                        ->badge(),
                    TextColumn::make('coa.name'),
                    TextColumn::make('issue_date')
                        ->label('Tgl. Terbit')
                        ->date()
                        ->sortable(),
                    TextColumn::make('submit.name')
                        ->visible($user->isManager()),
                ]),
                
                Stack::make([
                    TextColumn::make('status')->badge(),
                    TextColumn::make('subject')->weight('bold'),
                    TextColumn::make('approve.name')
                        ->visible(fn ($state) => $state),
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
            ]),
        ])
        ->defaultSort('due_date', 'desc')
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