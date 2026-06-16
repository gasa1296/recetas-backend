<?php

namespace App\Filament\Resources\Prescriptions\Pages;

use App\Filament\Resources\Prescriptions\PrescriptionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrescription extends CreateRecord
{
    use Concerns\HandlesFileUpload;
    protected static string $resource = PrescriptionResource::class;
}
