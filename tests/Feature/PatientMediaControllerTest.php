<?php

use App\Models\File;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
});

it('requires authentication for patient media endpoints', function () {
    $patient = Patient::factory()->create();

    $this->getJson("/api/patients/{$patient->id}/media")->assertUnauthorized();
    $this->postJson("/api/patients/{$patient->id}/media", [])->assertUnauthorized();
});

it('lists media files belonging to a patient', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $file1 = File::factory()->create([
        'model_type' => Patient::class,
        'model_id' => $patient->id,
        'category' => File::CATEGORY_RX,
        'title' => 'Radiografía tórax',
        'user_id' => $doctor->id,
    ]);

    $file2 = File::factory()->create([
        'model_type' => Patient::class,
        'model_id' => $patient->id,
        'category' => File::CATEGORY_SKIN_LESION,
        'title' => 'Lesión cutánea brazo',
        'user_id' => $doctor->id,
    ]);

    // Another patient's file
    $otherPatient = Patient::factory()->for($doctor)->create();
    File::factory()->create([
        'model_type' => Patient::class,
        'model_id' => $otherPatient->id,
        'user_id' => $doctor->id,
    ]);

    $response = $this->actingAs($doctor, 'sanctum')
        ->getJson("/api/patients/{$patient->id}/media")
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $file2->id) // latest first
        ->assertJsonPath('data.1.id', $file1->id);
});

it('filters patient media by category', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    File::factory()->create([
        'model_type' => Patient::class,
        'model_id' => $patient->id,
        'category' => File::CATEGORY_RX,
        'user_id' => $doctor->id,
    ]);

    File::factory()->create([
        'model_type' => Patient::class,
        'model_id' => $patient->id,
        'category' => File::CATEGORY_SKIN_LESION,
        'user_id' => $doctor->id,
    ]);

    $this->actingAs($doctor, 'sanctum')
        ->getJson("/api/patients/{$patient->id}/media?category=".File::CATEGORY_RX)
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.category', File::CATEGORY_RX);
});

it('filters patient media by evolution stage', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    File::factory()->create([
        'model_type' => Patient::class,
        'model_id' => $patient->id,
        'category' => File::CATEGORY_SKIN_LESION,
        'meta' => ['evolution_stage' => File::STAGE_BEFORE_TREATMENT],
        'user_id' => $doctor->id,
    ]);

    File::factory()->create([
        'model_type' => Patient::class,
        'model_id' => $patient->id,
        'category' => File::CATEGORY_SKIN_LESION,
        'meta' => ['evolution_stage' => File::STAGE_AFTER_TREATMENT],
        'user_id' => $doctor->id,
    ]);

    $this->actingAs($doctor, 'sanctum')
        ->getJson("/api/patients/{$patient->id}/media?evolution_stage=".File::STAGE_AFTER_TREATMENT)
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.evolution_stage', File::STAGE_AFTER_TREATMENT);
});

it('allows a doctor to upload an RX image for a patient', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $fakeImage = UploadedFile::fake()->image('torax_rx.png', 1200, 800);

    $response = $this->actingAs($doctor, 'sanctum')
        ->postJson("/api/patients/{$patient->id}/media", [
            'file' => $fakeImage,
            'category' => File::CATEGORY_RX,
            'title' => 'RX de Tórax PA',
            'description' => 'Sin hallazgos pleuropulmonares agudos',
            'evolution_stage' => File::STAGE_BEFORE_TREATMENT,
        ])
        ->assertCreated()
        ->assertJsonPath('data.title', 'RX de Tórax PA')
        ->assertJsonPath('data.category', File::CATEGORY_RX)
        ->assertJsonPath('data.evolution_stage', File::STAGE_BEFORE_TREATMENT)
        ->assertJsonPath('data.is_image', true);

    $fileId = $response->json('data.id');

    $this->assertDatabaseHas('files', [
        'id' => $fileId,
        'model_type' => Patient::class,
        'model_id' => $patient->id,
        'category' => File::CATEGORY_RX,
        'user_id' => $doctor->id,
        'title' => 'RX de Tórax PA',
    ]);

    $file = File::find($fileId);
    Storage::disk('local')->assertExists($file->path);
});

it('allows a doctor to upload a procedure video for a patient', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $fakeVideo = UploadedFile::fake()->create('marcha_paciente.mp4', 5000, 'video/mp4');

    $response = $this->actingAs($doctor, 'sanctum')
        ->postJson("/api/patients/{$patient->id}/media", [
            'file' => $fakeVideo,
            'category' => File::CATEGORY_PROCEDURE_VIDEO,
            'title' => 'Evaluación de Marcha',
            'description' => 'Marcha claudicante miembro inferior izquierdo',
        ])
        ->assertCreated()
        ->assertJsonPath('data.category', File::CATEGORY_PROCEDURE_VIDEO)
        ->assertJsonPath('data.is_video', true);

    $fileId = $response->json('data.id');
    $file = File::find($fileId);
    Storage::disk('local')->assertExists($file->path);
});

it('validates file types and max size on media upload', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    // Invalid extension .exe
    $invalidFile = UploadedFile::fake()->create('malware.exe', 100, 'application/x-msdownload');

    $this->actingAs($doctor, 'sanctum')
        ->postJson("/api/patients/{$patient->id}/media", [
            'file' => $invalidFile,
            'category' => File::CATEGORY_RX,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['file']);
});

it('allows viewing and streaming a patient media file', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $path = 'patients/'.$patient->id.'/media/test_photo.jpg';
    Storage::disk('local')->put($path, 'fake-image-binary-content');

    $file = File::factory()->create([
        'model_type' => Patient::class,
        'model_id' => $patient->id,
        'path' => $path,
        'filename' => 'test_photo.jpg',
        'mime_type' => 'image/jpeg',
        'user_id' => $doctor->id,
    ]);

    $this->actingAs($doctor, 'sanctum')
        ->getJson("/api/patients/{$patient->id}/media/{$file->id}")
        ->assertOk()
        ->assertJsonPath('data.filename', 'test_photo.jpg');

    $streamResponse = $this->actingAs($doctor, 'sanctum')
        ->get("/api/patients/{$patient->id}/media/{$file->id}/stream")
        ->assertOk()
        ->assertHeader('Content-Type', 'image/jpeg');

    expect($streamResponse->streamedContent())->toBe('fake-image-binary-content');
});

it('allows downloading a patient media file', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $path = 'patients/'.$patient->id.'/media/download_doc.pdf';
    Storage::disk('local')->put($path, 'sample-pdf-content');

    $file = File::factory()->create([
        'model_type' => Patient::class,
        'model_id' => $patient->id,
        'path' => $path,
        'filename' => 'download_doc.pdf',
        'mime_type' => 'application/pdf',
        'user_id' => $doctor->id,
    ]);

    $response = $this->actingAs($doctor, 'sanctum')
        ->get("/api/patients/{$patient->id}/media/{$file->id}/download")
        ->assertOk()
        ->assertHeader('content-disposition', 'attachment; filename=download_doc.pdf');
});

it('allows updating media title notes and evolution stage', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $file = File::factory()->create([
        'model_type' => Patient::class,
        'model_id' => $patient->id,
        'title' => 'Título viejo',
        'description' => 'Nota previa',
        'user_id' => $doctor->id,
    ]);

    $this->actingAs($doctor, 'sanctum')
        ->putJson("/api/patients/{$patient->id}/media/{$file->id}", [
            'title' => 'Nuevo título revisado',
            'description' => 'Evolución favorable a los 7 días',
            'evolution_stage' => File::STAGE_AFTER_TREATMENT,
        ])
        ->assertOk()
        ->assertJsonPath('data.title', 'Nuevo título revisado')
        ->assertJsonPath('data.description', 'Evolución favorable a los 7 días')
        ->assertJsonPath('data.evolution_stage', File::STAGE_AFTER_TREATMENT);

    $this->assertDatabaseHas('files', [
        'id' => $file->id,
        'title' => 'Nuevo título revisado',
    ]);
});

it('allows deleting a media file and cleans physical storage', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $path = 'patients/'.$patient->id.'/media/to_delete.jpg';
    Storage::disk('local')->put($path, 'temporary-bytes');

    $file = File::factory()->create([
        'model_type' => Patient::class,
        'model_id' => $patient->id,
        'path' => $path,
        'user_id' => $doctor->id,
    ]);

    $this->actingAs($doctor, 'sanctum')
        ->deleteJson("/api/patients/{$patient->id}/media/{$file->id}")
        ->assertNoContent();

    Storage::disk('local')->assertMissing($path);
    $this->assertSoftDeleted('files', ['id' => $file->id]);
});

it('returns 404 when requesting a media file of another patient', function () {
    $doctor = User::factory()->create();
    $patient1 = Patient::factory()->for($doctor)->create();
    $patient2 = Patient::factory()->for($doctor)->create();

    $file = File::factory()->create([
        'model_type' => Patient::class,
        'model_id' => $patient1->id,
        'user_id' => $doctor->id,
    ]);

    $this->actingAs($doctor, 'sanctum')
        ->getJson("/api/patients/{$patient2->id}/media/{$file->id}")
        ->assertNotFound();
});
