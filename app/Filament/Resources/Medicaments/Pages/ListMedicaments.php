<?php

namespace App\Filament\Resources\Medicaments\Pages;

use App\Filament\Resources\Medicaments\MedicamentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMedicaments extends ListRecords
{
    protected static string $resource = MedicamentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
