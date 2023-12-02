<?php

namespace App\Filament\Resources\ConsultingRoomResource\Pages;

use App\Filament\Resources\ConsultingRoomResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsultingRooms extends ListRecords
{
    protected static string $resource = ConsultingRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
