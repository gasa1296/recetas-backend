<?php

namespace App\Filament\Resources\PrescriptionTemplates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PrescriptionTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'first_name')
                    ->label('Médico')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
