<?php

namespace App\Filament\Resources\PrescriptionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\{Select, Textarea};
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EquipmentRelationManager extends RelationManager
{
    protected static string $relationship = 'equipment';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('equipment_id')
                    ->relationship('equipment', 'name')
                    ->required(),
                Textarea::make('add'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('equipment.name')
            ->columns([
                Tables\Columns\TextColumn::make('equipment.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
