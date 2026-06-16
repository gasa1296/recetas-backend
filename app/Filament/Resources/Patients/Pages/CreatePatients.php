<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientsResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePatients extends CreateRecord
{
    protected static string $resource = PatientsResource::class;
}
