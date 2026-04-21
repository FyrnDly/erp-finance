<?php

namespace App\Filament\Resources\Expenses\Tables;

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

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        /** @var User $user */
        $user = Auth::user();
        return $table->columns([
            Grid::make(4)->schema([
                Stack::make([
                    TextColumn::make('code')
                        ->searchable()
                        ->badge(),
                    TextColumn::make('coa.name'),
                    TextColumn::make('date')
                        ->label('Tanggal Nota')
                        ->date()
                        ->sortable(),
                    TextColumn::make('submit.name')
                        ->visible($user->isManager()),
                ]),
                Stack::make([
                    TextColumn::make('status')->badge(),
                    TextColumn::make('approved_date')
                        ->label('Tanggal Persetujuan')
                        ->date()
                        ->sortable()
                        ->visible(fn ($state) => $state),
                    TextColumn::make('approve.name')
                        ->visible(fn ($state) => $state),
                    TextColumn::make('refusal')
                        ->limit(150)
                        ->color('gray')
                        ->size('sm')
                        ->visible(fn ($state) => $state),
                ]),
                TextColumn::make('description')
                    ->limit(150)
                    ->color('gray')
                    ->size('sm'),
                TextColumn::make('amount')
                    ->money('IDR')
                    ->alignEnd()
                    ->weight('bold'),
            ]),
        ])
        ->defaultSort('date', 'desc')
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
