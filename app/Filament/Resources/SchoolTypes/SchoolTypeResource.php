<?php

namespace App\Filament\Resources\SchoolTypes;

use App\Filament\Resources\SchoolTypes\Pages\EditSchoolType;
use App\Filament\Resources\SchoolTypes\Pages\ManageSchoolTypes;
use App\Filament\Resources\SchoolTypes\RelationManagers\SubjectsRelationManager;
use App\Models\SchoolType;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SchoolTypeResource extends Resource
{
    protected static ?string $model = SchoolType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static ?string $modelLabel = 'Tip šole';

    protected static ?string $pluralModelLabel = 'Tipi šol';

    protected static ?string $navigationLabel = 'Tipi šol';

    protected static string|UnitEnum|null $navigationGroup = 'Šifranti';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Naziv')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('sort_order')
                    ->label('Vrstni red')
                    ->numeric()
                    ->default(0),
                Select::make('subjects')
                    ->label('Predmeti')
                    ->relationship('subjects', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Naziv')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                TextColumn::make('subjects.name')
                    ->label('Predmeti')
                    ->badge(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(fn (SchoolType $record): string => static::getUrl('edit', ['record' => $record]));
    }

    public static function getRelations(): array
    {
        return [
            SubjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSchoolTypes::route('/'),
            'edit' => EditSchoolType::route('/{record}/edit'),
        ];
    }
}
