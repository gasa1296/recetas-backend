<?php

namespace App\Filament\Resources\Prescriptions\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
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
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $pivot = [
                            'dosage' => $data['dosage'],
                            'frequency' => $data['frequency'],
                            'duration' => $data['duration'],
                        ];
                        unset($data['dosage'], $data['frequency'], $data['duration']);

                        return [...$data, ...$pivot];
                    }),
                AttachAction::make()
                    ->form(fn (Schema $schema): Schema => $schema->components([
                        TextInput::make('dosage')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('frequency')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('duration')
                            ->required()
                            ->maxLength(255),
                    ]))
                    ->mutateFormDataUsing(function (array $data): array {
                        $pivot = [
                            'dosage' => $data['dosage'],
                            'frequency' => $data['frequency'],
                            'duration' => $data['duration'],
                        ];
                        unset($data['dosage'], $data['frequency'], $data['duration']);

                        return $pivot;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        $data['dosage'] = $data['pivot']['dosage'] ?? '';
                        $data['frequency'] = $data['pivot']['frequency'] ?? '';
                        $data['duration'] = $data['pivot']['duration'] ?? '';
                        unset($data['pivot']);

                        return $data;
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        $pivot = [
                            'dosage' => $data['dosage'],
                            'frequency' => $data['frequency'],
                            'duration' => $data['duration'],
                        ];
                        unset($data['dosage'], $data['frequency'], $data['duration']);

                        return $pivot;
                    }),
                DetachAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
