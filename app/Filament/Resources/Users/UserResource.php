<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ManageUserComments;
use App\Filament\Resources\Users\Pages\ManageUserDownloadedDocuments;
use App\Filament\Resources\Users\Pages\ManageUserRatings;
use App\Filament\Resources\Users\Pages\ManageUserUploadedDocuments;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Filament\Resources\Users\Widgets\UserStatsOverview;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $modelLabel = 'Uporabnik';

    protected static ?string $pluralModelLabel = 'Uporabniki';

    protected static ?string $navigationLabel = 'Uporabniki';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'email';

    public static function getGloballySearchableAttributes(): array
    {
        return ['email', 'name', 'display_name'];
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            UserStatsOverview::class,
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewUser::class,
            EditUser::class,
            ManageUserUploadedDocuments::class,
            ManageUserDownloadedDocuments::class,
            ManageUserComments::class,
            ManageUserRatings::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
            'uploaded-documents' => ManageUserUploadedDocuments::route('/{record}/uploaded-documents'),
            'downloaded-documents' => ManageUserDownloadedDocuments::route('/{record}/downloaded-documents'),
            'comments' => ManageUserComments::route('/{record}/comments'),
            'ratings' => ManageUserRatings::route('/{record}/ratings'),
        ];
    }
}
