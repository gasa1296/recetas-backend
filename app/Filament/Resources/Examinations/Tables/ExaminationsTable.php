<?php

namespace App\Filament\Resources\Examinations\Tables;

use App\Models\Examination;
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

class ExaminationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.name')
                    ->label('Paciente')
                    ->sortable()
                    ->searchable(query: function ($query, string $search) {
                        $query->whereHas('patient', function ($q) use ($search) {
                            $q->where(DB::raw("CONCAT_WS(' ', first_name, last_name)"), 'LIKE', "%{$search}%");
                        });
                    }),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Examination::TYPE_LABORATORY => 'info',
                        Examination::TYPE_IMAGING => 'primary',
                        Examination::TYPE_PATHOLOGY => 'warning',
                        Examination::TYPE_CARDIOLOGY => 'danger',
                        default => 'secondary',
                    }),
                TextColumn::make('examined_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('laboratory_name')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Examination::STATUS_COMPLETED => 'success',
                        Examination::STATUS_REVIEWED => 'info',
                        default => 'warning',
                    }),
                TextColumn::make('user.name')
                    ->label('Médico')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('type')
                    ->options(array_combine(Examination::VALID_TYPES, Examination::VALID_TYPES)),
                SelectFilter::make('status')
                    ->options(array_combine(Examination::VALID_STATUSES, Examination::VALID_STATUSES)),
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
