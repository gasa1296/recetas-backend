<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    // 1. Modify form data before it goes to the database
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['signature_hash'] = hash('sha256', Str::random(64));

        return $data;
    }
}
