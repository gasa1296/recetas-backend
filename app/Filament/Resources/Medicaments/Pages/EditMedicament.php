<?php

namespace App\Filament\Resources\Medicaments\Pages;

use App\Filament\Resources\Medicaments\MedicamentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditMedicament extends EditRecord
{
    protected static string $resource = MedicamentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
