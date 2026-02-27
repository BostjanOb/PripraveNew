<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Osnovni podatki')
                    ->schema([
                        TextEntry::make('display_name')
                            ->label('Prikazno ime'),
                        TextEntry::make('name')
                            ->label('Ime'),
                        TextEntry::make('email')
                            ->label('E-pošta'),
                        TextEntry::make('role')
                            ->label('Vloga')
                            ->badge(),
                        IconEntry::make('email_verified_at')
                            ->label('E-pošta potrjena')
                            ->boolean()
                            ->getStateUsing(fn (User $record): bool => filled($record->email_verified_at)),
                        TextEntry::make('created_at')
                            ->label('Ustvarjen')
                            ->dateTime('d.m.Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Posodobljen')
                            ->dateTime('d.m.Y H:i'),
                    ]),
                Section::make('Avatar')
                    ->schema([
                        ImageEntry::make('avatar_url')
                            ->label('Avatar')
                            ->circular(),
                        TextEntry::make('avatar_path')
                            ->label('Pot do avatarja'),
                    ])
                    ->visible(fn (User $record): bool => filled($record->avatar_path)),
                Section::make('Statistika')
                    ->schema([
                        TextEntry::make('upload_count')
                            ->label('Naloženi dokumenti')
                            ->state(fn (User $record): int => $record->uploadCount()),
                        TextEntry::make('download_count')
                            ->label('Prenosi')
                            ->state(fn (User $record): int => $record->downloadCount()),
                        TextEntry::make('saved_count')
                            ->label('Shranjeni dokumenti')
                            ->state(fn (User $record): int => $record->savedCount()),
                        TextEntry::make('comment_count')
                            ->label('Komentarji')
                            ->state(fn (User $record): int => $record->commentCount()),
                        TextEntry::make('member_years')
                            ->label('Leta članstva')
                            ->state(fn (User $record): float => $record->memberYears()),
                    ]),
            ]);
    }
}
