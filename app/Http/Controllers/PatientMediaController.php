<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientMediaUpdateRequest;
use App\Http\Requests\PatientMediaUploadRequest;
use App\Http\Resources\PatientMediaResource;
use App\Models\File;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PatientMediaController extends Controller
{
    /**
     * Display a listing of the media files for a patient.
     */
    public function index(Request $request, Patient $patient): AnonymousResourceCollection
    {
        $query = $patient->media()->with('user');

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->filled('evolution_stage')) {
            $query->where('meta->evolution_stage', $request->query('evolution_stage'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('filename', 'like', "%{$search}%");
            });
        }

        $perPage = $request->integer('per_page', 20);
        $media = $query->paginate($perPage);

        return PatientMediaResource::collection($media);
    }

    /**
     * Store a newly created media file for a patient.
     */
    public function store(PatientMediaUploadRequest $request, Patient $patient): JsonResponse
    {
        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $mimeType = $uploadedFile->getClientMimeType() ?: $uploadedFile->getMimeType();
        $size = $uploadedFile->getSize();
        $extension = $uploadedFile->getClientOriginalExtension() ?: 'bin';

        // Secure private storage path
        $storedPath = $uploadedFile->store("patients/{$patient->id}/media", 'local');

        $meta = $request->input('meta', []);
        if ($request->filled('evolution_stage')) {
            $meta['evolution_stage'] = $request->input('evolution_stage');
        }

        $file = $patient->media()->create([
            'type' => $extension,
            'category' => $request->input('category'),
            'title' => $request->input('title') ?: pathinfo($originalName, PATHINFO_FILENAME),
            'description' => $request->input('description'),
            'mime_type' => $mimeType,
            'size' => $size,
            'meta' => $meta,
            'user_id' => auth()->id(),
            'location' => 'local',
            'path' => $storedPath,
            'filename' => $originalName,
        ]);

        return (new PatientMediaResource($file->load('user')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified media metadata.
     */
    public function show(Patient $patient, File $file): PatientMediaResource
    {
        $this->validateOwnership($patient, $file);

        return new PatientMediaResource($file->load('user'));
    }

    /**
     * Stream the specified media file securely with caching and range support.
     */
    public function stream(Patient $patient, File $file): BinaryFileResponse
    {
        $this->validateOwnership($patient, $file);

        if (! Storage::disk('local')->exists($file->path)) {
            abort(404, 'El archivo físico no se encuentra disponible.');
        }

        $path = Storage::disk('local')->path($file->path);

        return response()->file($path, [
            'Content-Type' => $file->mime_type ?: 'application/octet-stream',
            'Cache-Control' => 'private, max-age=86400',
            'Accept-Ranges' => 'bytes',
        ]);
    }

    /**
     * Download the specified media file as an attachment.
     */
    public function download(Patient $patient, File $file): BinaryFileResponse
    {
        $this->validateOwnership($patient, $file);

        if (! Storage::disk('local')->exists($file->path)) {
            abort(404, 'El archivo físico no se encuentra disponible.');
        }

        $path = Storage::disk('local')->path($file->path);

        return response()->download($path, $file->filename, [
            'Content-Type' => $file->mime_type ?: 'application/octet-stream',
        ]);
    }

    /**
     * Update the specified media file metadata.
     */
    public function update(PatientMediaUpdateRequest $request, Patient $patient, File $file): PatientMediaResource
    {
        $this->validateOwnership($patient, $file);

        $data = $request->validated();

        if ($request->has('category')) {
            $file->category = $data['category'];
        }

        if ($request->has('title')) {
            $file->title = $data['title'];
        }

        if ($request->has('description')) {
            $file->description = $data['description'];
        }

        $meta = $file->meta ?? [];
        if ($request->has('meta')) {
            $meta = array_merge($meta, $request->input('meta', []));
        }
        if ($request->has('evolution_stage')) {
            $meta['evolution_stage'] = $request->input('evolution_stage');
        }
        $file->meta = $meta;

        $file->save();

        return new PatientMediaResource($file->load('user'));
    }

    /**
     * Remove the specified media file from storage.
     */
    public function destroy(Patient $patient, File $file): Response
    {
        $this->validateOwnership($patient, $file);

        if (Storage::disk('local')->exists($file->path)) {
            Storage::disk('local')->delete($file->path);
        }

        $file->delete();

        return response()->noContent();
    }

    /**
     * Validate that the file belongs to the requested patient.
     */
    protected function validateOwnership(Patient $patient, File $file): void
    {
        if ($file->model_type !== Patient::class || (int) $file->model_id !== (int) $patient->id) {
            abort(404, 'El archivo no pertenece al paciente indicado.');
        }
    }
}
