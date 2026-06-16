<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name1')
                    ->default(null),
                TextInput::make('last_name2')
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                Textarea::make('phone')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('gender')
                    ->default(null),
                DatePicker::make('birth_date'),
                Select::make('user_id')
                    ->relationship('user', 'id')
                    ->required(),
            ]);
    }
}
