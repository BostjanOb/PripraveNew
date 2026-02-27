<?php

namespace App\Filament\Resources\Grades;

use App\Filament\Resources\Grades\Pages\ManageGrades;
use App\Models\Grade;
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

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmarkSquare;

    protected static ?string $modelLabel = 'Razred';

    protected static ?string $pluralModelLabel = 'Razredi';

    protected static ?string $navigationLabel = 'Razredi';

    protected static string|UnitEnum|null $navigationGroup = 'Šifranti';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_type_id')
                    ->label('Tip šole')
                    ->relationship('schoolType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->label('Naziv')
                    ->required()
                    ->maxLength(255),
                TextInput::make('sort_order')
                    ->label('Vrstni red')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Vrstni red')
                    ->sortable(),
                TextColumn::make('schoolType.name')
                    ->label('Tip šole')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Naziv')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
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
            'index' => ManageGrades::route('/'),
        ];
    }
}
