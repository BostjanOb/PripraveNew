<?php

namespace App\Filament\Resources\Comments;

use App\Filament\Resources\Comments\Pages\ManageComments;
use App\Models\Comment;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $modelLabel = 'Komentar';

    protected static ?string $pluralModelLabel = 'Komentarji';

    protected static ?string $navigationLabel = 'Komentarji';

    protected static string|UnitEnum|null $navigationGroup = 'Gradiva';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('document_id')
                    ->label('Gradivo')
                    ->relationship('document', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('user_id')
                    ->label('Uporabnik')
                    ->relationship('user', 'display_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Textarea::make('text')
                    ->label('Komentar')
                    ->rows(4)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document.title')
                    ->label('Gradivo')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('user.display_name')
                    ->label('Uporabnik')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('text')
                    ->label('Komentar')
                    ->searchable()
                    ->limit(60),
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
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageComments::route('/'),
        ];
    }
}
