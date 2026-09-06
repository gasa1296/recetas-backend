<?php

namespace App\Filament\Resources\Laboratories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LaboratoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->default(null),
                TextInput::make('country')
                    ->default(null),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
