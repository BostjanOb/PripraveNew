<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_name')
                    ->label('Prikazno ime')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('E-pošta')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Vloga')
                    ->badge()
                    ->sortable(),
                IconColumn::make('email_verified_at')
                    ->label('E-pošta potrjena')
                    ->boolean()
                    ->getStateUsing(fn (User $record): bool => filled($record->email_verified_at))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Ustvarjen')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn (User $record): bool => $record->is(Auth::user())),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            $records
                                ->reject(fn (User $record): bool => $record->is(Auth::user()))
                                ->each->delete();
                        }),
                ]),
            ]);
    }
}
