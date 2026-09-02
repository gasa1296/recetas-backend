<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('guard_name')
                    ->required()
                    ->default(config('auth.defaults.guard', 'web'))
                    ->maxLength(255)
                    ->readOnly(),
                Section::make('Permisos')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->hiddenLabel()
                            ->relationship('permissions', 'name')
                            ->columns(2)
                            ->searchable()
                            ->bulkToggleable()
                            ->extraAttributes(['class' => 'max-h-96 overflow-y-auto']),
                    ])
                    ->collapsible(),
            ]);
    }
}
