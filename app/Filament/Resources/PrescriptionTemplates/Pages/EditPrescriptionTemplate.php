<?php

namespace App\Filament\Resources\PrescriptionTemplates\Pages;

use App\Filament\Resources\PrescriptionTemplates\PrescriptionTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPrescriptionTemplate extends EditRecord
{
    protected static string $resource = PrescriptionTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
