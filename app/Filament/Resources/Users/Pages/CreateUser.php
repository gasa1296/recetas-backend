<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\CertificateService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['signature_hash'] = hash('sha256', Str::random(64));

        return $data;
    }

    protected function afterCreate(): void
    {
        $certificateService = app(CertificateService::class);
        $certificate = $certificateService->generateForUser($this->record);

        $this->record->update([
            'certificate_path' => $certificate['certificate_path'],
            'certificate_key_path' => $certificate['key_path'],
            'certificate_expires_at' => $certificate['expires_at'],
        ]);
    }
}
