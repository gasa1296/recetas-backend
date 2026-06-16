<?php

namespace App\Filament\Resources\Prescriptions\Pages\Concerns;

use Illuminate\Http\UploadedFile;

trait HandlesFileUpload
{
    protected function afterSave(): void
    {
        $file = $this->form->getState()['file'] ?? null;

        if ($file instanceof UploadedFile) {
            $this->record->handleUploadFile($file);
        }
    }
}