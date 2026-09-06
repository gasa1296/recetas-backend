<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Models\Appointment;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'first_name')
                    ->label('Médico')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('patient_id')
                    ->relationship('patient')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->label('Paciente')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->label('Consulta')
                    ->searchable()
                    ->preload(),
                Select::make('specialty_id')
                    ->relationship('specialty', 'name')
                    ->label('Especialidad')
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('starts_at')
                    ->required(),
                DateTimePicker::make('ends_at')
                    ->required(),
                TextInput::make('reason')
                    ->default(null),
                Select::make('status')
                    ->options(array_combine(Appointment::VALID_STATUSES, Appointment::VALID_STATUSES))
                    ->required(),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('reminder_channel')
                    ->options([
                        'email' => 'Email',
                        'whatsapp' => 'WhatsApp',
                        'sms' => 'SMS',
                    ])
                    ->required(),
                Toggle::make('reminder_enabled')
                    ->default(true),
                DateTimePicker::make('reminder_sent_at')
                    ->default(null),
            ]);
    }
}
