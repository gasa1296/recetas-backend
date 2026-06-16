<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->default(null),
                TextInput::make('last_name1')
                    ->default(null),
                TextInput::make('last_name2')
                    ->default(null),
                Textarea::make('phone')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('gender')
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Toggle::make('is_admin')
                    ->required(),
            ]);
    }
}
