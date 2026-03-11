<?php

namespace App\Filament\Resources\Subjects;

use App\Filament\Resources\Subjects\Pages\EditSubject;
use App\Filament\Resources\Subjects\Pages\ManageSubjects;
use App\Filament\Resources\Subjects\RelationManagers\SchoolTypesRelationManager;
use App\Models\Subject;
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

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $modelLabel = 'Predmet';

    protected static ?string $pluralModelLabel = 'Predmeti';

    protected static ?string $navigationLabel = 'Predmeti';

    protected static string|UnitEnum|null $navigationGroup = 'Šifranti';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Naziv')
                    ->required()
                    ->maxLength(255),
                Select::make('schoolTypes')
                    ->label('Tipi šol')
                    ->relationship('schoolTypes', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->required(),
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
                TextColumn::make('schoolTypes.name')
                    ->label('Tipi šol')
                    ->bulleted()
                    ->searchable(),
            ])
            ->defaultSort('name')
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

    public static function getRelations(): array
    {
        return [
            SchoolTypesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSubjects::route('/'),
            'edit' => EditSubject::route('/{record}/edit'),
        ];
    }
}
