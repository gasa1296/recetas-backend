<?php

namespace App\Services\Media;

use App\Models\File;
use App\Models\Prescription;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileStorageService
{
    /**
     * Get the storage disk to use for files.
     */
    public function getDisk(?string $disk = null): string
    {
        return $disk ?: config('filesystems.default', 'local');
    }

    /**
     * Check if the physical file for a given File model exists on disk.
     */
    public function exists(File $file): bool
    {
        $disk = $this->getDisk($file->location);

        return Storage::disk($disk)->exists($file->path);
    }

    /**
     * Safely delete the physical file from storage and delete the File model.
     */
    public function deleteFile(File $file): bool
    {
        $disk = $this->getDisk($file->location);

        if (Storage::disk($disk)->exists($file->path)) {
            Storage::disk($disk)->delete($file->path);
        }

        return (bool) $file->delete();
    }

    /**
     * Create a streamed inline response for viewing the file in the browser.
     *
     * @param  array<string, string>  $headers
     */
    public function response(File $file, ?string $filename = null, array $headers = []): StreamedResponse
    {
        $disk = $this->getDisk($file->location);
        $name = $filename ?: $file->filename;

        $defaultHeaders = [
            'Content-Type' => $file->mime_type ?: 'application/octet-stream',
        ];

        return Storage::disk($disk)->response($file->path, $name, array_merge($defaultHeaders, $headers));
    }

    /**
     * Create a download response for the file.
     *
     * @param  array<string, string>  $headers
     */
    public function download(File $file, ?string $filename = null, array $headers = []): StreamedResponse
    {
        $disk = $this->getDisk($file->location);
        $name = $filename ?: $file->filename;

        $defaultHeaders = [
            'Content-Type' => $file->mime_type ?: 'application/octet-stream',
        ];

        return Storage::disk($disk)->download($file->path, $name, array_merge($defaultHeaders, $headers));
    }

    /**
     * Store an uploaded file to a directory on disk and return normalized metadata.
     *
     * @return array{path: string, location: string, filename: string, mime_type: string, size: int, type: string}
     */
    public function storeUploadedFile(UploadedFile $uploadedFile, string $directory, ?string $disk = null): array
    {
        $disk = $this->getDisk($disk);
        $storedPath = $uploadedFile->store($directory, $disk);

        return [
            'path' => $storedPath,
            'location' => $disk,
            'filename' => $uploadedFile->getClientOriginalName(),
            'mime_type' => $uploadedFile->getClientMimeType() ?: $uploadedFile->getMimeType() ?: 'application/octet-stream',
            'size' => $uploadedFile->getSize() ?: 0,
            'type' => $uploadedFile->getClientOriginalExtension() ?: 'bin',
        ];
    }

    /**
     * Store a generated PDF content for a prescription model, cleaning up any previous file of that type.
     */
    public function storePrescriptionPdf(Prescription $prescription, string|UploadedFile $file, string $type = 'unsigned'): File
    {
        $disk = $this->getDisk();

        $existingFiles = $prescription->files()->where('type', $type)->get();
        foreach ($existingFiles as $oldFile) {
            $this->deleteFile($oldFile);
        }

        $name = Str::uuid().'.pdf';
        $path = date('Y').'/'.date('m').'/'.$name;
        Storage::disk($disk)->put($path, $file);

        return $prescription->files()->create([
            'path' => $path,
            'type' => $type,
            'location' => $disk,
            'filename' => $name,
            'mime_type' => 'application/pdf',
            'user_id' => $prescription->user_id,
        ]);
    }
}
