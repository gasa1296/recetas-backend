<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'first_name')
                    ->label('Médico Responsable')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->default(null),
                TextInput::make('identification')
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
            ]);
    }
}
