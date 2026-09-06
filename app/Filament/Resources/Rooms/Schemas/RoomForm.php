<?php

namespace App\Filament\Resources\Rooms\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('identification')
                    ->required(),
                TextInput::make('zip')
                    ->required(),
                TextInput::make('address')
                    ->default(null),
                KeyValue::make('phone')
                    ->default([]),
                Select::make('user_id')
                    ->relationship('user', 'first_name')
                    ->label('Médico')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
