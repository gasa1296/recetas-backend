<?php

namespace App\Filament\Resources\Appointments\Tables;

use App\Models\Appointment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Médico')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('patient.name')
                    ->label('Paciente')
                    ->sortable()
                    ->searchable(query: function ($query, string $search) {
                        $query->whereHas('patient', function ($q) use ($search) {
                            $q->where(DB::raw("CONCAT_WS(' ', first_name, last_name)"), 'LIKE', "%{$search}%");
                        });
                    }),
                TextColumn::make('room.name')
                    ->label('Consulta')
                    ->searchable(),
                TextColumn::make('specialty.name')
                    ->label('Especialidad')
                    ->searchable(),
                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Appointment::STATUS_CANCELLED => 'danger',
                        Appointment::STATUS_NO_SHOW => 'gray',
                        Appointment::STATUS_COMPLETED => 'success',
                        Appointment::STATUS_IN_CONSULTATION => 'info',
                        Appointment::STATUS_IN_WAITING_ROOM => 'warning',
                        Appointment::STATUS_CONFIRMED => 'primary',
                        default => 'secondary',
                    }),
                TextColumn::make('reason')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('reminder_enabled')
                    ->boolean()
                    ->label('Recordatorio'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(array_combine(Appointment::VALID_STATUSES, Appointment::VALID_STATUSES)),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
