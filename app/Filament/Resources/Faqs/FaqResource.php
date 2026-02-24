<?php

namespace App\Filament\Resources\Faqs;

use App\Filament\Resources\Faqs\Pages\ManageFaqs;
use App\Models\Faq;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\File;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'FAQ';

    protected static ?string $navigationLabel = 'FAQ';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('question')
                    ->label('Vprašanje')
                    ->required()
                    ->maxLength(255),
                Textarea::make('answer')
                    ->label('Odgovor')
                    ->rows(4)
                    ->required()
                    ->columnSpanFull(),
                Select::make('icon')
                    ->label('Ikona')
                    ->options(static::iconOptions())
                    ->searchable()
                    ->required(),
                Select::make('icon_background_color')
                    ->label('Barva ozadja ikone')
                    ->options(Faq::iconBackgroundColorOptions())
                    ->required()
                    ->native(false),
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
                TextColumn::make('question')
                    ->label('Vprašanje')
                    ->searchable()
                    ->limit(70),
                TextColumn::make('icon')
                    ->label('Ikona')
                    ->badge(),
                TextColumn::make('icon_background_color')
                    ->label('Barva')
                    ->formatStateUsing(fn (string $state): string => Faq::iconBackgroundColorOptions()[$state] ?? $state)
                    ->badge(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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

    /** @return array<string, string> */
    protected static function iconOptions(): array
    {
        return collect(File::files(resource_path('svg/regular')))
            ->filter(fn (\SplFileInfo $file): bool => $file->getExtension() === 'svg')
            ->mapWithKeys(function (\SplFileInfo $file): array {
                $icon = $file->getBasename('.svg');

                return [$icon => $icon];
            })
            ->sortKeys()
            ->all();
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageFaqs::route('/'),
        ];
    }
}
