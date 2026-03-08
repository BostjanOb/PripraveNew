<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use BackedEnum;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManageDocumentComments extends ManageRelatedRecords
{
    protected static string $resource = DocumentResource::class;

    protected static string $relationship = 'comments';

    protected static ?string $navigationLabel = 'Komentarji';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeft;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.display_name')
                    ->label('Uporabnik')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('text')
                    ->label('Komentar')
                    ->limit(100)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Datum')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
