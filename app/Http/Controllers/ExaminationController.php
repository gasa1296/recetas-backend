<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExaminationFileRequest;
use App\Http\Requests\ExaminationRequest;
use App\Http\Resources\ExaminationResource;
use App\Http\Resources\PatientMediaResource;
use App\Models\Examination;
use App\Models\File;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ExaminationController extends Controller
{
    /**
     * Display a listing of the examinations for a patient.
     */
    public function index(Request $request, Patient $patient): AnonymousResourceCollection
    {
        $this->authorizePatient($patient);

        $query = $patient->examinations()->with(['files.user', 'user']);

        if ($request->filled('type')) {
            $query->type($request->query('type'));
        }

        if ($request->filled('status')) {
            $query->status($request->query('status'));
        }

        if ($request->filled('from') || $request->filled('to')) {
            $query->dateRange($request->query('from'), $request->query('to'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('laboratory_name', 'like', "%{$search}%")
                    ->orWhere('findings', 'like', "%{$search}%");
            });
        }

        $perPage = $request->integer('per_page', 15);
        $examinations = $query->paginate($perPage);

        return ExaminationResource::collection($examinations);
    }

    /**
     * Store a newly created examination for a patient.
     */
    public function store(ExaminationRequest $request, Patient $patient): JsonResponse
    {
        $this->authorizePatient($patient);

        $data = $request->validated();
        unset($data['file']);

        $data['user_id'] = auth()->id();

        $examination = $patient->examinations()->create($data);

        if ($request->hasFile('file')) {
            $this->attachFileToExamination($request->file('file'), $examination, $patient);
        }

        return (new ExaminationResource($examination->load(['files.user', 'user', 'patient'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified examination.
     */
    public function show(Patient $patient, Examination $examination): ExaminationResource
    {
        $this->validateOwnership($patient, $examination);

        return new ExaminationResource(
            $examination->load(['files.user', 'user', 'patient', 'prescription'])
        );
    }

    /**
     * Update the specified examination in storage.
     */
    public function update(ExaminationRequest $request, Patient $patient, Examination $examination): ExaminationResource
    {
        $this->validateOwnership($patient, $examination);

        $data = $request->validated();
        unset($data['file']);

        $examination->update($data);

        if ($request->hasFile('file')) {
            $this->attachFileToExamination($request->file('file'), $examination, $patient);
        }

        return new ExaminationResource($examination->load(['files.user', 'user', 'patient']));
    }

    /**
     * Remove the specified examination from storage.
     */
    public function destroy(Patient $patient, Examination $examination): Response
    {
        $this->validateOwnership($patient, $examination);

        $examination->delete();

        return response()->noContent();
    }

    /**
     * Add an additional file attachment to the examination.
     */
    public function addFile(ExaminationFileRequest $request, Patient $patient, Examination $examination): JsonResponse
    {
        $this->validateOwnership($patient, $examination);

        $file = $this->attachFileToExamination(
            $request->file('file'),
            $examination,
            $patient,
            $request->input('title'),
            $request->input('description')
        );

        return (new PatientMediaResource($file->load('user')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Remove a file attachment from the examination.
     */
    public function removeFile(Patient $patient, Examination $examination, File $file): Response
    {
        $this->validateOwnership($patient, $examination);

        if ($file->model_type !== Examination::class || (int) $file->model_id !== (int) $examination->id) {
            abort(404, 'El archivo no pertenece al examen indicado.');
        }

        if (Storage::disk('local')->exists($file->path)) {
            Storage::disk('local')->delete($file->path);
        }

        $file->delete();

        return response()->noContent();
    }

    /**
     * Helper to store and attach an uploaded file to an examination.
     */
    protected function attachFileToExamination($uploadedFile, Examination $examination, Patient $patient, ?string $title = null, ?string $description = null): File
    {
        $originalName = $uploadedFile->getClientOriginalName();
        $mimeType = $uploadedFile->getClientMimeType() ?: $uploadedFile->getMimeType();
        $size = $uploadedFile->getSize();
        $extension = $uploadedFile->getClientOriginalExtension() ?: 'bin';

        $storedPath = $uploadedFile->store("patients/{$patient->id}/examinations/{$examination->id}", 'local');

        return $examination->files()->create([
            'type' => $extension,
            'category' => File::CATEGORY_EXAMINATION,
            'title' => $title ?: pathinfo($originalName, PATHINFO_FILENAME),
            'description' => $description,
            'mime_type' => $mimeType,
            'size' => $size,
            'meta' => [
                'examination_id' => $examination->id,
                'examination_name' => $examination->name,
                'examination_type' => $examination->type,
            ],
            'user_id' => auth()->id(),
            'location' => 'local',
            'path' => $storedPath,
            'filename' => $originalName,
        ]);
    }

    /**
     * Validate that the examination belongs to the specified patient and doctor.
     */
    protected function validateOwnership(Patient $patient, Examination $examination): void
    {
        $this->authorizePatient($patient);

        if ((int) $examination->patient_id !== (int) $patient->id) {
            abort(404, 'El examen no pertenece al paciente indicado.');
        }
    }

    /**
     * Authorize that the patient belongs to the authenticated medic.
     */
    protected function authorizePatient(Patient $patient): void
    {
        if ((int) $patient->user_id !== (int) auth()->id()) {
            abort(404, 'Paciente no encontrado.');
        }
    }
}
