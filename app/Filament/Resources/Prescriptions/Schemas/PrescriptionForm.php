<?php

namespace App\Filament\Resources\Prescriptions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PrescriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('temp')
                    ->numeric()
                    ->default(null),
                TextInput::make('weight')
                    ->numeric()
                    ->default(null),
                TextInput::make('height')
                    ->numeric()
                    ->default(null),
                TextInput::make('pressure')
                    ->default(null),
                TextInput::make('saturation')
                    ->default(null),
                TextInput::make('ppm')
                    ->default(null),
                Textarea::make('allergy')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('diagnostic')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('diet')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('comments')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('user_id')
                    ->relationship('user', 'id')
                    ->required(),
                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->required(),
                Select::make('patient_id')
                    ->relationship('patient', 'id')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
