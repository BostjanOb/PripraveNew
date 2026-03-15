<?php

namespace App\Filament\Resources\Documents;

use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Filament\Resources\Documents\Pages\ManageDocumentComments;
use App\Filament\Resources\Documents\Pages\ManageDocumentDownloads;
use App\Filament\Resources\Documents\Pages\ManageDocumentRatings;
use App\Filament\Resources\Documents\Pages\ViewDocument;
use App\Models\Document;
use App\Models\Subject;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $modelLabel = 'Gradivo';

    protected static ?string $pluralModelLabel = 'Gradiva';

    protected static ?string $navigationLabel = 'Gradiva';

    protected static string|UnitEnum|null $navigationGroup = 'Gradiva';

    protected static ?int $navigationSort = 10;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Avtor')
                    ->relationship('user', 'display_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('category_id')
                    ->label('Kategorija')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('school_type_id')
                    ->label('Tip šole')
                    ->relationship('schoolType', 'name')
                    ->live()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('subject_id', null);
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('grade_id')
                    ->label('Razred')
                    ->relationship('grade', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('subject_id')
                    ->label('Predmet')
                    ->options(
                        fn (Get $get): array => Subject::when(
                            $get('school_type_id'),
                            fn ($query, $schoolTypeId) => $query->forSchoolType((int) $schoolTypeId),
                        )
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('title')
                    ->label('Naslov')
                    ->required()
                    ->maxLength(200)
                    ->columnSpanFull(),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Opis')
                    ->rows(4)
                    ->columnSpanFull(),
                TextInput::make('topic')
                    ->label('Tema')
                    ->maxLength(200),
                TextInput::make('keywords')
                    ->label('Ključne besede')
                    ->maxLength(500),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Osnovni podatki')
                    ->columnSpanFull()
                    ->columns()
                    ->schema([
                        TextEntry::make('user.display_name')
                            ->label('Avtor'),
                        TextEntry::make('title')
                            ->label('Naslov'),
                        TextEntry::make('slug')
                            ->label('Slug'),
                        TextEntry::make('category.name')
                            ->label('Kategorija'),
                        TextEntry::make('schoolType.name')
                            ->label('Tip šole'),
                        TextEntry::make('grade.name')
                            ->label('Razred'),
                        TextEntry::make('subject.name')
                            ->label('Predmet'),
                        TextEntry::make('downloads_count')
                            ->label('Prenosi'),
                        TextEntry::make('views_count')
                            ->label('Ogledi'),
                        TextEntry::make('rating_count')
                            ->label('Število ocen'),
                        TextEntry::make('rating_avg')
                            ->label('Povprečna ocena'),
                        TextEntry::make('created_at')
                            ->label('Ustvarjeno')
                            ->dateTime('d.m.Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Posodobljeno')
                            ->dateTime('d.m.Y H:i'),

                        TextEntry::make('description')
                            ->label('Opis')
                            ->columnSpanFull(),
                        TextEntry::make('topic')
                            ->label('Tema'),
                        TextEntry::make('keywords')
                            ->label('Ključne besede'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Naslov')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('user.display_name')
                    ->label('Avtor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Kategorija')
                    ->searchable(),
                TextColumn::make('subject.name')
                    ->label('Predmet')
                    ->searchable(),
                TextColumn::make('downloads_count')
                    ->label('Prenosi')
                    ->sortable(),
                TextColumn::make('views_count')
                    ->label('Ogledi')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Ustvarjeno')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('editFrontend')
                    ->label('Uredi')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->url(fn (Document $record): string => static::getFrontendEditUrl($record)),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->recordUrl(fn (Document $record): string => static::getUrl('view', ['record' => $record]));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewDocument::class,
            ManageDocumentDownloads::class,
            ManageDocumentComments::class,
            ManageDocumentRatings::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'view' => ViewDocument::route('/{record}'),
            'downloads' => ManageDocumentDownloads::route('/{record}/downloads'),
            'comments' => ManageDocumentComments::route('/{record}/comments'),
            'ratings' => ManageDocumentRatings::route('/{record}/ratings'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('view', ['record' => $record]);
    }

    public static function getFrontendEditUrl(Document $record): string
    {
        return route('document.create', ['uredi' => $record->getKey()]);
    }
}
