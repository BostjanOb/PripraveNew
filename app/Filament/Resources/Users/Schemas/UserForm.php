<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('display_name')
                    ->label('Prikazno ime')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name')
                    ->label('Ime')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('E-pošta')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('role')
                    ->label('Vloga')
                    ->options([
                        'admin' => 'Admin',
                        'user' => 'Uporabnik',
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('avatar_path')
                    ->label('Pot do avatarja')
                    ->maxLength(255),
                DateTimePicker::make('email_verified_at')
                    ->label('E-pošta potrjena')
                    ->seconds(false),
                TextInput::make('password')
                    ->label('Geslo')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->rule(Password::defaults())
                    ->confirmed()
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                TextInput::make('password_confirmation')
                    ->label('Potrdi geslo')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(false),
            ]);
    }
}
