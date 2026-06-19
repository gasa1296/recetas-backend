<?php

namespace App\Filament\Resources\Prescriptions\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MedicamentsRelationManager extends RelationManager
{
    protected static string $relationship = 'medicaments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('dosage')
                    ->required()
                    ->maxLength(255),
                TextInput::make('frequency')
                    ->required()
                    ->maxLength(255),
                TextInput::make('duration')
                    ->required()
                    ->maxLength(255),
                TextInput::make('medicament_quantity')
                    ->required()
                    ->maxLength(255),
                TextInput::make('medicament_quantity_letters')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('pivot.dosage'),
                TextColumn::make('pivot.frequency'),
                TextColumn::make('pivot.duration'),
                TextColumn::make('pivot.medicament_quantity'),
                TextColumn::make('pivot.medicament_quantity_letters'),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
