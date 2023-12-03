<?php

namespace App\Filament\Resources\ConsultingRoomResource\Pages;

use App\Filament\Resources\ConsultingRoomResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConsultingRoom extends EditRecord
{
    protected static string $resource = ConsultingRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
