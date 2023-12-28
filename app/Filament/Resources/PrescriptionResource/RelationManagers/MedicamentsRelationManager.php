<?php

namespace App\Filament\Resources\PrescriptionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\{Textarea, TextInput};
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
                TextInput::make('medicament_id')
                    ->numeric()
                    ->required(),
                Textarea::make('add'),
                TextInput::make('dose'),
                TextInput::make('way'),
                TextInput::make('frequency')
                    ->numeric(),
                TextInput::make('duration')
                    ->numeric(),
                TextInput::make('quantity')
                    ->numeric(),
                TextInput::make('quantity_exp')
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
