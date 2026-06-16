<?php

namespace App\Filament\Resources\Medicaments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MedicamentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('salt')
                    ->required(),
                TextInput::make('type')
                    ->required(),
                TextInput::make('group')
                    ->required(),
            ]);
    }
}
