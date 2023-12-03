<?php

namespace App\Filament\Resources\PrescriptionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\{Select, Textarea, TextInput};
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MedicamentsRelationManager extends RelationManager
{
    protected static string $relationship = 'medicaments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('medicament_id')
                    ->relationship('medicament', 'name')
                    ->required(),
                TextInput::make('dose'),
                TextInput::make('way'),
                TextInput::make('frequency')
                    ->numeric(),
                TextInput::make('duration')
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('medicament.name')
            ->columns([
                Tables\Columns\TextColumn::make('medicament.name'),
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
