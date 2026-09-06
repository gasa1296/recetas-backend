<?php

namespace App\Filament\Resources\Examinations\Schemas;

use App\Models\Examination;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExaminationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('patient_id')
                    ->relationship('patient')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->label('Paciente')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'first_name')
                    ->label('Médico')
                    ->searchable()
                    ->preload(),
                Select::make('prescription_id')
                    ->relationship('prescription')
                    ->getOptionLabelFromRecordUsing(fn ($record) => '#'.$record->id)
                    ->label('Receta')
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->required(),
                Select::make('type')
                    ->options(array_combine(Examination::VALID_TYPES, Examination::VALID_TYPES))
                    ->required(),
                DatePicker::make('examined_at'),
                TextInput::make('laboratory_name')
                    ->default(null),
                Textarea::make('findings')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(array_combine(Examination::VALID_STATUSES, Examination::VALID_STATUSES))
                    ->required(),
            ]);
    }
}
