<?php

namespace App\Filament\Resources\Reports;

use App\Enums\ReportStatus;
use App\Filament\Resources\Reports\Pages\ManageReports;
use App\Models\Report;
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

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?string $modelLabel = 'Prijava';

    protected static ?string $pluralModelLabel = 'Prijave';

    protected static ?string $navigationLabel = 'Prijave';

    protected static string|UnitEnum|null $navigationGroup = 'Gradiva';

    protected static ?int $navigationSort = 30;

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
                Select::make('report_reason_id')
                    ->label('Razlog prijave')
                    ->relationship('reportReason', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(ReportStatus::class)
                    ->required()
                    ->native(false),
                Textarea::make('message')
                    ->label('Sporočilo')
                    ->rows(4)
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
                TextColumn::make('reportReason.name')
                    ->label('Razlog')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Ustvarjena')
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
            'index' => ManageReports::route('/'),
        ];
    }
}
