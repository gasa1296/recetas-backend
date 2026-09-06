<?php

use App\Http\Requests\AppointmentRequest;
use App\Http\Requests\ExaminationFileRequest;
use App\Http\Requests\ExaminationRequest;
use App\Http\Requests\FinishPrescriptionRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PatientMediaUploadRequest;
use App\Http\Requests\PatientRequest;
use App\Http\Requests\PrescriptionRequest;
use App\Http\Requests\PrescriptionTemplateRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetRequest;
use App\Http\Requests\ResetRequestRequest;
use App\Http\Requests\RoomRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\SpecialtyRequest;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;

uses(RefreshDatabase::class);

test('attributes method returns Spanish translations when locale is es', function () {
    App::setLocale('es');

    $examFileReq = new ExaminationFileRequest;
    expect($examFileReq->attributes()['file'])->toBe('archivo adjunto (PDF o imagen)');
    expect($examFileReq->attributes()['title'])->toBe('título del adjunto');
    expect($examFileReq->attributes()['description'])->toBe('descripción u observaciones');

    $examReq = new ExaminationRequest;
    expect($examReq->attributes()['name'])->toBe('nombre del examen');
    expect($examReq->attributes()['type'])->toBe('tipo de examen');
    expect($examReq->attributes()['examined_at'])->toBe('fecha de realización');
    expect($examReq->attributes()['laboratory_name'])->toBe('laboratorio o centro de diagnóstico');

    $mediaReq = new PatientMediaUploadRequest;
    expect($mediaReq->attributes()['file'])->toBe('archivo médico');
    expect($mediaReq->attributes()['category'])->toBe('categoría médica');

    $patientReq = new PatientRequest;
    expect($patientReq->attributes()['first_name'])->toBe('nombre');
    expect($patientReq->attributes()['last_name'])->toBe('apellido');
    expect($patientReq->attributes()['birth_date'])->toBe('fecha de nacimiento');
    expect($patientReq->attributes()['identification'])->toBe('cédula o identificación');

    $roomReq = new RoomRequest;
    expect($roomReq->attributes()['name'])->toBe('nombre del consultorio');

    $loginReq = new LoginRequest;
    expect($loginReq->attributes()['email'])->toBe('correo electrónico');
    expect($loginReq->attributes()['password'])->toBe('contraseña');

    $appReq = new AppointmentRequest;
    expect($appReq->attributes()['starts_at'])->toBe('fecha de inicio');
    expect($appReq->attributes()['ends_at'])->toBe('fecha de fin');
    expect($appReq->attributes()['reason'])->toBe('motivo');

    $rxReq = new PrescriptionRequest;
    expect($rxReq->attributes()['temp'])->toBe('temperatura');
    expect($rxReq->attributes()['pressure'])->toBe('presión arterial');
    expect($rxReq->attributes()['medicaments'])->toBe('medicamentos');

    $tplReq = new PrescriptionTemplateRequest;
    expect($tplReq->attributes()['name'])->toBe('nombre de la plantilla');

    $profileReq = new ProfileRequest;
    expect($profileReq->attributes()['first_name'])->toBe('nombre');
    expect($profileReq->attributes()['specialty'])->toBe('especialidad');

    $registerReq = new RegisterRequest;
    expect($registerReq->attributes()['email'])->toBe('correo electrónico');
    expect($registerReq->attributes()['password'])->toBe('contraseña');

    $resetReq = new ResetRequest;
    expect($resetReq->attributes()['token'])->toBe('token');

    $resetReqReq = new ResetRequestRequest;
    expect($resetReqReq->attributes()['email'])->toBe('correo electrónico');

    $searchReq = new SearchRequest;
    expect($searchReq->attributes()['search'])->toBe('término de búsqueda');

    $specialtyReq = new SpecialtyRequest;
    expect($specialtyReq->attributes()['name'])->toBe('nombre de la especialidad');

    $finishRxReq = new FinishPrescriptionRequest;
    expect($finishRxReq->attributes()['signature'])->toBe('firma');
});

test('attributes method returns English translations when locale is en', function () {
    App::setLocale('en');

    $examFileReq = new ExaminationFileRequest;
    expect($examFileReq->attributes()['file'])->toBe('attached file (PDF or image)');
    expect($examFileReq->attributes()['title'])->toBe('attachment title');
    expect($examFileReq->attributes()['description'])->toBe('description or observations');

    $examReq = new ExaminationRequest;
    expect($examReq->attributes()['name'])->toBe('examination name');
    expect($examReq->attributes()['type'])->toBe('examination type');
    expect($examReq->attributes()['examined_at'])->toBe('examination date');
    expect($examReq->attributes()['laboratory_name'])->toBe('laboratory or diagnostic center');

    $mediaReq = new PatientMediaUploadRequest;
    expect($mediaReq->attributes()['file'])->toBe('medical file');
    expect($mediaReq->attributes()['category'])->toBe('medical category');

    $patientReq = new PatientRequest;
    expect($patientReq->attributes()['first_name'])->toBe('first name');
    expect($patientReq->attributes()['last_name'])->toBe('last name');
    expect($patientReq->attributes()['birth_date'])->toBe('birth date');
    expect($patientReq->attributes()['identification'])->toBe('identification');

    $roomReq = new RoomRequest;
    expect($roomReq->attributes()['name'])->toBe('room name');

    $loginReq = new LoginRequest;
    expect($loginReq->attributes()['email'])->toBe('email address');
    expect($loginReq->attributes()['password'])->toBe('password');

    $appReq = new AppointmentRequest;
    expect($appReq->attributes()['starts_at'])->toBe('start time');
    expect($appReq->attributes()['ends_at'])->toBe('end time');
    expect($appReq->attributes()['reason'])->toBe('reason');

    $rxReq = new PrescriptionRequest;
    expect($rxReq->attributes()['temp'])->toBe('temperature');
    expect($rxReq->attributes()['pressure'])->toBe('blood pressure');
    expect($rxReq->attributes()['medicaments'])->toBe('medications');

    $tplReq = new PrescriptionTemplateRequest;
    expect($tplReq->attributes()['name'])->toBe('template name');

    $profileReq = new ProfileRequest;
    expect($profileReq->attributes()['first_name'])->toBe('first name');
    expect($profileReq->attributes()['specialty'])->toBe('specialty');

    $registerReq = new RegisterRequest;
    expect($registerReq->attributes()['email'])->toBe('email address');
    expect($registerReq->attributes()['password'])->toBe('password');

    $resetReq = new ResetRequest;
    expect($resetReq->attributes()['token'])->toBe('token');

    $resetReqReq = new ResetRequestRequest;
    expect($resetReqReq->attributes()['email'])->toBe('email address');

    $searchReq = new SearchRequest;
    expect($searchReq->attributes()['search'])->toBe('search query');

    $specialtyReq = new SpecialtyRequest;
    expect($specialtyReq->attributes()['name'])->toBe('specialty name');

    $finishRxReq = new FinishPrescriptionRequest;
    expect($finishRxReq->attributes()['signature'])->toBe('signature');
});

test('HTTP request with Accept-Language: es returns validation errors and attributes in Spanish', function () {
    $doctor = User::factory()->create();

    $response = $this->actingAs($doctor, 'sanctum')
        ->withHeader('Accept-Language', 'es')
        ->postJson('/api/patients', []);

    $response->assertStatus(422);

    $errors = $response->json('errors');
    expect($errors)->toHaveKey('first_name');
    expect($errors['first_name'][0])->toContain('El campo nombre es obligatorio.');

    expect($errors)->toHaveKey('birth_date');
    expect($errors['birth_date'][0])->toContain('El campo fecha de nacimiento es obligatorio.');
});

test('HTTP request with Accept-Language: en returns validation errors and attributes in English', function () {
    $doctor = User::factory()->create();

    $response = $this->actingAs($doctor, 'sanctum')
        ->withHeader('Accept-Language', 'en')
        ->postJson('/api/patients', []);

    $response->assertStatus(422);

    $errors = $response->json('errors');
    expect($errors)->toHaveKey('first_name');
    expect($errors['first_name'][0])->toContain('The first name field is required.');

    expect($errors)->toHaveKey('birth_date');
    expect($errors['birth_date'][0])->toContain('The birth date field is required.');
});

test('HTTP request with X-Locale: es returns examination validation errors in Spanish', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $response = $this->actingAs($doctor, 'sanctum')
        ->withHeader('X-Locale', 'es')
        ->postJson("/api/patients/{$patient->id}/examinations", []);

    $response->assertStatus(422);

    $errors = $response->json('errors');
    expect($errors)->toHaveKey('name');
    expect($errors['name'][0])->toContain('nombre del examen');
    expect($errors)->toHaveKey('type');
    expect($errors['type'][0])->toContain('tipo de examen');
});

test('HTTP request with X-Locale: en returns examination validation errors in English', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $response = $this->actingAs($doctor, 'sanctum')
        ->withHeader('X-Locale', 'en')
        ->postJson("/api/patients/{$patient->id}/examinations", []);

    $response->assertStatus(422);

    $errors = $response->json('errors');
    expect($errors)->toHaveKey('name');
    expect($errors['name'][0])->toContain('examination name');
    expect($errors)->toHaveKey('type');
    expect($errors['type'][0])->toContain('examination type');
});

test('HTTP request with query param lang returns patient media validation in expected language', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    // Spanish via query
    $responseEs = $this->actingAs($doctor, 'sanctum')
        ->postJson("/api/patients/{$patient->id}/media?lang=es", []);

    $responseEs->assertStatus(422);
    $errorsEs = $responseEs->json('errors');
    expect($errorsEs['file'][0])->toContain('archivo médico');
    expect($errorsEs['category'][0])->toContain('categoría médica');

    // English via query
    $responseEn = $this->actingAs($doctor, 'sanctum')
        ->postJson("/api/patients/{$patient->id}/media?lang=en", []);

    $responseEn->assertStatus(422);
    $errorsEn = $responseEn->json('errors');
    expect($errorsEn['file'][0])->toContain('medical file');
    expect($errorsEn['category'][0])->toContain('medical category');
});

test('appointment schedule conflict messages localize properly', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    Appointment::factory()->create([
        'user_id' => $doctor->id,
        'patient_id' => $patient->id,
        'starts_at' => '2026-10-10 10:00:00',
        'ends_at' => '2026-10-10 11:00:00',
    ]);

    // In Spanish
    $resEs = $this->actingAs($doctor, 'sanctum')
        ->withHeader('Accept-Language', 'es')
        ->postJson('/api/appointments', [
            'patient_id' => $patient->id,
            'starts_at' => '2026-10-10 10:30:00',
            'ends_at' => '2026-10-10 11:30:00',
        ]);

    $resEs->assertStatus(422);
    expect($resEs->json('errors.starts_at.0'))->toBe('El médico ya tiene una cita programada en ese rango de horario.');

    // In English
    $resEn = $this->actingAs($doctor, 'sanctum')
        ->withHeader('Accept-Language', 'en')
        ->postJson('/api/appointments', [
            'patient_id' => $patient->id,
            'starts_at' => '2026-10-10 10:30:00',
            'ends_at' => '2026-10-10 11:30:00',
        ]);

    $resEn->assertStatus(422);
    expect($resEn->json('errors.starts_at.0'))->toBe('The doctor already has an appointment scheduled in that time slot.');
});
