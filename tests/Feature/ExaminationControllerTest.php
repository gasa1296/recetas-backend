<?php

use App\Models\Examination;
use App\Models\File;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Room;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
});

it('requires authentication to access examination endpoints', function () {
    $patient = Patient::factory()->create();

    $this->getJson("/api/patients/{$patient->id}/examinations")->assertUnauthorized();
    $this->postJson("/api/patients/{$patient->id}/examinations", [])->assertUnauthorized();
});

it('lists examinations belonging to a patient', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $exam1 = Examination::factory()->create([
        'patient_id' => $patient->id,
        'user_id' => $doctor->id,
        'name' => 'Perfil 20',
        'examined_at' => now()->subDays(5)->format('Y-m-d'),
    ]);

    $exam2 = Examination::factory()->create([
        'patient_id' => $patient->id,
        'user_id' => $doctor->id,
        'name' => 'Urocultivo',
        'examined_at' => now()->subDays(1)->format('Y-m-d'),
    ]);

    // Another patient's exam
    $otherPatient = Patient::factory()->for($doctor)->create();
    Examination::factory()->create([
        'patient_id' => $otherPatient->id,
        'user_id' => $doctor->id,
    ]);

    $response = $this->actingAs($doctor, 'sanctum')
        ->getJson("/api/patients/{$patient->id}/examinations")
        ->assertOk()
        ->assertJsonCount(2, 'data');

    expect($response->json('data.0.id'))->toBe($exam2->id); // latest examined_at first
});

it('filters examinations by type and status', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    Examination::factory()->create([
        'patient_id' => $patient->id,
        'user_id' => $doctor->id,
        'name' => 'Biometría',
        'type' => Examination::TYPE_LABORATORY,
        'status' => Examination::STATUS_COMPLETED,
    ]);

    Examination::factory()->create([
        'patient_id' => $patient->id,
        'user_id' => $doctor->id,
        'name' => 'Ecocardiograma',
        'type' => Examination::TYPE_CARDIOLOGY,
        'status' => Examination::STATUS_PENDING,
    ]);

    $response = $this->actingAs($doctor, 'sanctum')
        ->getJson("/api/patients/{$patient->id}/examinations?type=".Examination::TYPE_CARDIOLOGY)
        ->assertOk()
        ->assertJsonCount(1, 'data');

    expect($response->json('data.0.name'))->toBe('Ecocardiograma');
});

it('filters examinations by date range', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    Examination::factory()->create([
        'patient_id' => $patient->id,
        'user_id' => $doctor->id,
        'name' => 'Examen Antiguo',
        'examined_at' => '2026-01-10',
    ]);

    Examination::factory()->create([
        'patient_id' => $patient->id,
        'user_id' => $doctor->id,
        'name' => 'Examen Reciente',
        'examined_at' => '2026-09-01',
    ]);

    $response = $this->actingAs($doctor, 'sanctum')
        ->getJson("/api/patients/{$patient->id}/examinations?from=2026-08-01&to=2026-09-30")
        ->assertOk()
        ->assertJsonCount(1, 'data');

    expect($response->json('data.0.name'))->toBe('Examen Reciente');
});

it('allows a doctor to create an examination with an attached lab PDF', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $fakePdf = UploadedFile::fake()->create('reporte_laboratorio.pdf', 1500, 'application/pdf');

    $payload = [
        'name' => 'Química Sanguínea Completa',
        'type' => Examination::TYPE_LABORATORY,
        'examined_at' => '2026-09-02',
        'laboratory_name' => 'Laboratorio Pasteur',
        'findings' => 'Glicemia: 95 mg/dL. Urea: 25 mg/dL. Creatinina: 0.9 mg/dL.',
        'status' => Examination::STATUS_COMPLETED,
        'file' => $fakePdf,
    ];

    $response = $this->actingAs($doctor, 'sanctum')
        ->postJson("/api/patients/{$patient->id}/examinations", $payload)
        ->assertCreated()
        ->assertJsonPath('data.name', 'Química Sanguínea Completa')
        ->assertJsonPath('data.laboratory_name', 'Laboratorio Pasteur')
        ->assertJsonPath('data.files_count', 1);

    $examId = $response->json('data.id');

    $this->assertDatabaseHas('examinations', [
        'id' => $examId,
        'patient_id' => $patient->id,
        'user_id' => $doctor->id,
        'name' => 'Química Sanguínea Completa',
    ]);

    $this->assertDatabaseHas('files', [
        'model_type' => Examination::class,
        'model_id' => $examId,
        'category' => File::CATEGORY_EXAMINATION,
        'filename' => 'reporte_laboratorio.pdf',
    ]);
});

it('allows a doctor to view detailed examination with attached files', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $exam = Examination::factory()->create([
        'patient_id' => $patient->id,
        'user_id' => $doctor->id,
        'name' => 'Perfil Tiroideo',
    ]);

    File::factory()->create([
        'model_type' => Examination::class,
        'model_id' => $exam->id,
        'category' => File::CATEGORY_EXAMINATION,
        'filename' => 'tsh_t4.pdf',
        'user_id' => $doctor->id,
    ]);

    $response = $this->actingAs($doctor, 'sanctum')
        ->getJson("/api/patients/{$patient->id}/examinations/{$exam->id}")
        ->assertOk()
        ->assertJsonPath('data.name', 'Perfil Tiroideo')
        ->assertJsonCount(1, 'data.files')
        ->assertJsonPath('data.files.0.filename', 'tsh_t4.pdf');
});

it('allows a doctor to update examination findings and status', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $exam = Examination::factory()->create([
        'patient_id' => $patient->id,
        'user_id' => $doctor->id,
        'name' => 'Urocultivo',
        'status' => Examination::STATUS_PENDING,
        'findings' => 'Pendiente por reporte de antibiograma',
    ]);

    $this->actingAs($doctor, 'sanctum')
        ->putJson("/api/patients/{$patient->id}/examinations/{$exam->id}", [
            'name' => 'Urocultivo Definitivo',
            'type' => Examination::TYPE_LABORATORY,
            'status' => Examination::STATUS_REVIEWED,
            'findings' => 'Negativo para desarrollo bacteriano a las 72h.',
        ])
        ->assertOk()
        ->assertJsonPath('data.status', Examination::STATUS_REVIEWED)
        ->assertJsonPath('data.findings', 'Negativo para desarrollo bacteriano a las 72h.');

    $this->assertDatabaseHas('examinations', [
        'id' => $exam->id,
        'name' => 'Urocultivo Definitivo',
        'status' => Examination::STATUS_REVIEWED,
    ]);
});

it('allows a doctor to add an additional attachment to an examination', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $exam = Examination::factory()->create([
        'patient_id' => $patient->id,
        'user_id' => $doctor->id,
    ]);

    $fakeImage = UploadedFile::fake()->image('resultado_escaneado.jpg');

    $response = $this->actingAs($doctor, 'sanctum')
        ->postJson("/api/patients/{$patient->id}/examinations/{$exam->id}/files", [
            'file' => $fakeImage,
            'title' => 'Hoja 2 de resultados',
            'description' => 'Firma y sello del bioanalista',
        ])
        ->assertCreated()
        ->assertJsonPath('data.title', 'Hoja 2 de resultados');

    $fileId = $response->json('data.id');

    $this->assertDatabaseHas('files', [
        'id' => $fileId,
        'model_type' => Examination::class,
        'model_id' => $exam->id,
        'title' => 'Hoja 2 de resultados',
    ]);
});

it('allows a doctor to remove an attachment from an examination', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $exam = Examination::factory()->create([
        'patient_id' => $patient->id,
        'user_id' => $doctor->id,
    ]);

    $path = "patients/{$patient->id}/examinations/{$exam->id}/doc.pdf";
    Storage::disk('local')->put($path, 'pdf-bytes');

    $file = File::factory()->create([
        'model_type' => Examination::class,
        'model_id' => $exam->id,
        'path' => $path,
        'user_id' => $doctor->id,
    ]);

    $this->actingAs($doctor, 'sanctum')
        ->deleteJson("/api/patients/{$patient->id}/examinations/{$exam->id}/files/{$file->id}")
        ->assertNoContent();

    Storage::disk('local')->assertMissing($path);
    $this->assertSoftDeleted('files', ['id' => $file->id]);
});

it('allows soft deleting an examination', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $exam = Examination::factory()->create([
        'patient_id' => $patient->id,
        'user_id' => $doctor->id,
    ]);

    $this->actingAs($doctor, 'sanctum')
        ->deleteJson("/api/patients/{$patient->id}/examinations/{$exam->id}")
        ->assertNoContent();

    $this->assertSoftDeleted('examinations', ['id' => $exam->id]);
});

it('returns 404 when accessing an examination of another patient', function () {
    $doctor = User::factory()->create();
    $patient1 = Patient::factory()->for($doctor)->create();
    $patient2 = Patient::factory()->for($doctor)->create();

    $exam = Examination::factory()->create([
        'patient_id' => $patient1->id,
        'user_id' => $doctor->id,
    ]);

    $this->actingAs($doctor, 'sanctum')
        ->getJson("/api/patients/{$patient2->id}/examinations/{$exam->id}")
        ->assertNotFound();
});

it('prevents a doctor from listing examinations of another doctor\'s patient', function () {
    $doctor1 = User::factory()->create();
    $doctor2 = User::factory()->create();
    $patient = Patient::factory()->for($doctor1)->create();

    Examination::factory()->create([
        'patient_id' => $patient->id,
        'user_id' => $doctor1->id,
    ]);

    $this->actingAs($doctor2, 'sanctum')
        ->getJson("/api/patients/{$patient->id}/examinations")
        ->assertNotFound();
});

it('prevents a doctor from creating an examination for another doctor\'s patient', function () {
    $doctor1 = User::factory()->create();
    $doctor2 = User::factory()->create();
    $patient = Patient::factory()->for($doctor1)->create();

    $payload = [
        'name' => 'Perfil Lipídico',
        'type' => Examination::TYPE_LABORATORY,
        'status' => Examination::STATUS_COMPLETED,
    ];

    $this->actingAs($doctor2, 'sanctum')
        ->postJson("/api/patients/{$patient->id}/examinations", $payload)
        ->assertNotFound();
});

it('prevents a doctor from viewing, updating or deleting an examination of another doctor\'s patient', function () {
    $doctor1 = User::factory()->create();
    $doctor2 = User::factory()->create();
    $patient = Patient::factory()->for($doctor1)->create();

    $exam = Examination::factory()->create([
        'patient_id' => $patient->id,
        'user_id' => $doctor1->id,
        'name' => 'Rayos X de Tórax',
    ]);

    // Show
    $this->actingAs($doctor2, 'sanctum')
        ->getJson("/api/patients/{$patient->id}/examinations/{$exam->id}")
        ->assertNotFound();

    // Update
    $this->actingAs($doctor2, 'sanctum')
        ->putJson("/api/patients/{$patient->id}/examinations/{$exam->id}", [
            'name' => 'Modificado',
            'type' => Examination::TYPE_IMAGING,
        ])
        ->assertNotFound();

    // Delete
    $this->actingAs($doctor2, 'sanctum')
        ->deleteJson("/api/patients/{$patient->id}/examinations/{$exam->id}")
        ->assertNotFound();

    $this->assertDatabaseHas('examinations', [
        'id' => $exam->id,
        'deleted_at' => null,
    ]);
});

it('prevents a doctor from adding or removing files from another doctor\'s patient examination', function () {
    $doctor1 = User::factory()->create();
    $doctor2 = User::factory()->create();
    $patient = Patient::factory()->for($doctor1)->create();

    $exam = Examination::factory()->create([
        'patient_id' => $patient->id,
        'user_id' => $doctor1->id,
    ]);

    $file = File::factory()->create([
        'model_type' => Examination::class,
        'model_id' => $exam->id,
        'user_id' => $doctor1->id,
    ]);

    $fakeImage = UploadedFile::fake()->image('prueba.jpg');

    // Add file
    $this->actingAs($doctor2, 'sanctum')
        ->postJson("/api/patients/{$patient->id}/examinations/{$exam->id}/files", [
            'file' => $fakeImage,
        ])
        ->assertNotFound();

    // Remove file
    $this->actingAs($doctor2, 'sanctum')
        ->deleteJson("/api/patients/{$patient->id}/examinations/{$exam->id}/files/{$file->id}")
        ->assertNotFound();

    $this->assertDatabaseHas('files', [
        'id' => $file->id,
        'deleted_at' => null,
    ]);
});

it('validates that attached prescription belongs to the authenticated doctor and patient', function () {
    $doctor1 = User::factory()->create();
    $doctor2 = User::factory()->create();

    $patient1 = Patient::factory()->for($doctor1)->create();
    $patient2 = Patient::factory()->for($doctor1)->create();

    $room1 = Room::factory()->for($doctor1)->create();
    $specialty1 = Specialty::factory()->for($doctor1)->create();

    $room2 = Room::factory()->for($doctor2)->create();
    $specialty2 = Specialty::factory()->for($doctor2)->create();

    // Prescription belonging to doctor2
    $rxOtherDoctor = Prescription::factory()
        ->for($patient1, 'patient')
        ->for($room2, 'room')
        ->for($specialty2, 'specialty')
        ->for($doctor2)
        ->create();

    // Prescription belonging to doctor1 but for patient2
    $rxOtherPatient = Prescription::factory()
        ->for($patient2, 'patient')
        ->for($room1, 'room')
        ->for($specialty1, 'specialty')
        ->for($doctor1)
        ->create();

    // Prescription belonging to doctor1 and patient1
    $rxValid = Prescription::factory()
        ->for($patient1, 'patient')
        ->for($room1, 'room')
        ->for($specialty1, 'specialty')
        ->for($doctor1)
        ->create();

    // 1. Trying to link another doctor's prescription fails validation
    $this->actingAs($doctor1, 'sanctum')
        ->postJson("/api/patients/{$patient1->id}/examinations", [
            'name' => 'Examen con receta ajena',
            'type' => Examination::TYPE_LABORATORY,
            'prescription_id' => $rxOtherDoctor->id,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['prescription_id']);

    // 2. Trying to link another patient's prescription fails validation
    $this->actingAs($doctor1, 'sanctum')
        ->postJson("/api/patients/{$patient1->id}/examinations", [
            'name' => 'Examen con receta de otro paciente',
            'type' => Examination::TYPE_LABORATORY,
            'prescription_id' => $rxOtherPatient->id,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['prescription_id']);

    // 3. Linking the valid prescription succeeds
    $this->actingAs($doctor1, 'sanctum')
        ->postJson("/api/patients/{$patient1->id}/examinations", [
            'name' => 'Examen con receta válida',
            'type' => Examination::TYPE_LABORATORY,
            'prescription_id' => $rxValid->id,
        ])
        ->assertCreated()
        ->assertJsonPath('data.prescription_id', $rxValid->id);
});
