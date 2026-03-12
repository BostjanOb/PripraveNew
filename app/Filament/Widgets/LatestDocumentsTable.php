<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Document;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestDocumentsTable extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Najnovejša gradiva';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Document::with(['user', 'subject', 'category'])
                ->whereNull('deleted_at')
                ->latest()
                ->limit(10))
            ->columns([
                TextColumn::make('title')
                    ->label('Naslov')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('user.display_name')
                    ->label('Avtor')
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->label('Predmet')
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Kategorija')
                    ->sortable(),
                TextColumn::make('downloads_count')
                    ->label('Prenosi')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Ustvarjeno')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->paginated(false)
            ->recordUrl(fn (Document $record): string => DocumentResource::getUrl('view', ['record' => $record]))
            ->emptyStateHeading('Ni novih gradiv.');
    }
}
