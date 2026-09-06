<?php

namespace App\Filament\Resources\PrescriptionTemplates\Pages;

use App\Filament\Resources\PrescriptionTemplates\PrescriptionTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrescriptionTemplates extends ListRecords
{
    protected static string $resource = PrescriptionTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
