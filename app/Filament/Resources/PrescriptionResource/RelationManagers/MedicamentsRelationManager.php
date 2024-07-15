<?php

namespace App\Filament\Resources\PrescriptionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
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
                Forms\Components\TextInput::make('medicament_id')
                    ->numeric()
                    ->required(),
                Forms\Components\Textarea::make('add'),
                Forms\Components\TextInput::make('dose'),
                Forms\Components\TextInput::make('way'),
                Forms\Components\TextInput::make('frequency')
                    ->numeric(),
                Forms\Components\TextInput::make('duration')
                    ->numeric(),
                Forms\Components\TextInput::make('quantity')
                    ->numeric(),
                Forms\Components\TextInput::make('quantity_exp')
                    ->numeric(),
                Forms\Components\TextInput::make('medicament_id')
                    ->numeric(),
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('type'),
                Forms\Components\TextInput::make('family'),
                Forms\Components\TextInput::make('group'),
                Forms\Components\TextInput::make('salt'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('medicament_id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('add')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('dose')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('way')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('frequency')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('duration')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity_exp')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('medicament_id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('family')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('group')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('salt')
                    ->sortable()
                    ->searchable(),
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
