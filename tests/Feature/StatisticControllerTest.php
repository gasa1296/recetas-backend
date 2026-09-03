<?php

use App\Models\Brand;
use App\Models\Laboratory;
use App\Models\Medicament;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Room;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->doctor = User::factory()->create();
    $this->specialty = Specialty::factory()->create(['user_id' => $this->doctor->id]);
    $this->room = Room::factory()->create(['user_id' => $this->doctor->id]);
    $this->patient = Patient::factory()->create();

    // Create laboratories and brands
    $this->labBayer = Laboratory::factory()->create(['name' => 'Bayer Test', 'country' => 'Alemania']);
    $this->labPfizer = Laboratory::factory()->create(['name' => 'Pfizer Test', 'country' => 'EEUU']);

    $this->brandAspirina = Brand::factory()->create(['laboratory_id' => $this->labBayer->id, 'name' => 'Aspirina Test']);
    $this->brandLipitor = Brand::factory()->create(['laboratory_id' => $this->labPfizer->id, 'name' => 'Lipitor Test']);

    $this->med1 = Medicament::factory()->create(['active_ingredient' => 'Acido Acetilsalicilico', 'type' => 'Comprimido', 'group' => 'Analgesico']);
    $this->med2 = Medicament::factory()->create(['active_ingredient' => 'Atorvastatina', 'type' => 'Comprimido', 'group' => 'Cardiovascular']);
});

it('requires authentication for statistics endpoints', function () {
    $this->getJson('/api/statistics/overview')->assertUnauthorized();
    $this->getJson('/api/statistics/by-medicament')->assertUnauthorized();
    $this->getJson('/api/statistics/by-brand')->assertUnauthorized();
    $this->getJson('/api/statistics/by-laboratory')->assertUnauthorized();
    $this->getJson('/api/statistics/by-patient')->assertUnauthorized();
    $this->getJson('/api/statistics/timeline')->assertUnauthorized();
});

it('returns general overview statistics', function () {
    // Create 2 prescriptions for this doctor
    $rx1 = Prescription::factory()->create([
        'user_id' => $this->doctor->id,
        'patient_id' => $this->patient->id,
        'room_id' => $this->room->id,
        'specialty_id' => $this->specialty->id,
        'status' => config('custom.prescription.status_keys.active'),
    ]);
    $rx1->medicaments()->attach($this->med1->id, [
        'dosage' => '100mg',
        'frequency' => '24h',
        'duration' => '30d',
        'medicament_quantity' => 30,
        'medicament_quantity_letters' => 'treinta',
        'recommended_brand' => 'Aspirina Test',
        'brand_id' => $this->brandAspirina->id,
        'laboratory_id' => $this->labBayer->id,
    ]);

    $rx2 = Prescription::factory()->create([
        'user_id' => $this->doctor->id,
        'patient_id' => $this->patient->id,
        'room_id' => $this->room->id,
        'specialty_id' => $this->specialty->id,
        'status' => config('custom.prescription.status_keys.draft'),
    ]);
    $rx2->medicaments()->attach($this->med2->id, [
        'dosage' => '20mg',
        'frequency' => '24h',
        'duration' => '30d',
        'medicament_quantity' => 30,
        'medicament_quantity_letters' => 'treinta',
        'recommended_brand' => 'Lipitor Test',
        'brand_id' => $this->brandLipitor->id,
        'laboratory_id' => $this->labPfizer->id,
    ]);

    $response = $this->actingAs($this->doctor, 'sanctum')
        ->getJson('/api/statistics/overview')
        ->assertOk()
        ->assertJsonStructure([
            'success',
            'data' => [
                'total_prescriptions',
                'active_prescriptions',
                'dispensed_prescriptions',
                'total_patients_attended',
                'total_medicaments_prescribed',
                'total_units_prescribed',
                'average_medicaments_per_prescription',
            ],
        ]);

    expect($response->json('data.total_prescriptions'))->toBe(2);
    expect($response->json('data.active_prescriptions'))->toBe(1);
    expect($response->json('data.total_medicaments_prescribed'))->toBe(2);
    expect($response->json('data.total_units_prescribed'))->toBe(60);
    expect($response->json('data.average_medicaments_per_prescription'))->toEqual(1.0);
});

it('returns statistics grouped by medicament with quantity and percentage', function () {
    $rx = Prescription::factory()->create([
        'user_id' => $this->doctor->id,
        'patient_id' => $this->patient->id,
        'room_id' => $this->room->id,
        'specialty_id' => $this->specialty->id,
    ]);
    $rx->medicaments()->attach($this->med1->id, [
        'dosage' => '100mg',
        'frequency' => '24h',
        'duration' => '30d',
        'medicament_quantity' => 30,
        'medicament_quantity_letters' => 'treinta',
    ]);
    $rx->medicaments()->attach($this->med2->id, [
        'dosage' => '20mg',
        'frequency' => '24h',
        'duration' => '15d',
        'medicament_quantity' => 15,
        'medicament_quantity_letters' => 'quince',
    ]);

    $response = $this->actingAs($this->doctor, 'sanctum')
        ->getJson('/api/statistics/by-medicament')
        ->assertOk()
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['medicament_id', 'active_ingredient', 'type', 'group', 'prescription_count', 'total_quantity', 'percentage'],
            ],
            'total_items',
        ]);

    expect($response->json('total_items'))->toBe(2);
    expect($response->json('data.0.prescription_count'))->toBe(1);
    expect($response->json('data.0.percentage'))->toEqual(50.0);
});

it('returns statistics grouped by recommended brand', function () {
    $rx = Prescription::factory()->create([
        'user_id' => $this->doctor->id,
        'patient_id' => $this->patient->id,
        'room_id' => $this->room->id,
        'specialty_id' => $this->specialty->id,
    ]);
    $rx->medicaments()->attach($this->med1->id, [
        'dosage' => '100mg',
        'frequency' => '24h',
        'duration' => '30d',
        'medicament_quantity' => 30,
        'medicament_quantity_letters' => 'treinta',
        'recommended_brand' => 'Aspirina Test',
        'brand_id' => $this->brandAspirina->id,
        'laboratory_id' => $this->labBayer->id,
    ]);

    $response = $this->actingAs($this->doctor, 'sanctum')
        ->getJson('/api/statistics/by-brand')
        ->assertOk()
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['brand_name', 'laboratory_name', 'prescription_count', 'percentage'],
            ],
            'total_branded',
        ]);

    expect($response->json('total_branded'))->toBe(1);
    expect($response->json('data.0.brand_name'))->toBe('Aspirina Test');
    expect($response->json('data.0.percentage'))->toEqual(100.0);
});

it('returns statistics grouped by laboratory', function () {
    $rx = Prescription::factory()->create([
        'user_id' => $this->doctor->id,
        'patient_id' => $this->patient->id,
        'room_id' => $this->room->id,
        'specialty_id' => $this->specialty->id,
    ]);
    $rx->medicaments()->attach($this->med1->id, [
        'dosage' => '100mg',
        'frequency' => '24h',
        'duration' => '30d',
        'medicament_quantity' => 30,
        'medicament_quantity_letters' => 'treinta',
        'brand_id' => $this->brandAspirina->id,
        'laboratory_id' => $this->labBayer->id,
    ]);

    $response = $this->actingAs($this->doctor, 'sanctum')
        ->getJson('/api/statistics/by-laboratory')
        ->assertOk()
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['laboratory_name', 'country', 'prescription_count', 'percentage'],
            ],
            'total_items',
        ]);

    expect($response->json('total_items'))->toBe(1);
    expect($response->json('data.0.laboratory_name'))->toBe('Bayer Test');
    expect($response->json('data.0.percentage'))->toEqual(100.0);
});

it('returns statistics grouped by patient', function () {
    $rx = Prescription::factory()->create([
        'user_id' => $this->doctor->id,
        'patient_id' => $this->patient->id,
        'room_id' => $this->room->id,
        'specialty_id' => $this->specialty->id,
    ]);
    $rx->medicaments()->attach($this->med1->id, [
        'dosage' => '100mg',
        'frequency' => '24h',
        'duration' => '30d',
        'medicament_quantity' => 30,
        'medicament_quantity_letters' => 'treinta',
    ]);

    $response = $this->actingAs($this->doctor, 'sanctum')
        ->getJson('/api/statistics/by-patient')
        ->assertOk()
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['patient_id', 'patient_name', 'identification', 'gender', 'prescriptions_count', 'distinct_medicaments', 'last_prescription_at'],
            ],
        ]);

    expect($response->json('data.0.patient_id'))->toBe($this->patient->id);
    expect($response->json('data.0.prescriptions_count'))->toBe(1);
    expect($response->json('data.0.distinct_medicaments'))->toBe(1);
});

it('returns statistics grouped by patient with date range filters without ambiguous column error', function () {
    $rx = Prescription::factory()->create([
        'user_id' => $this->doctor->id,
        'patient_id' => $this->patient->id,
        'room_id' => $this->room->id,
        'specialty_id' => $this->specialty->id,
        'created_at' => now(),
    ]);

    $response = $this->actingAs($this->doctor, 'sanctum')
        ->getJson('/api/statistics/by-patient?from='.now()->subDays(30)->toDateString().'&to='.now()->toDateString())
        ->assertOk();

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.patient_id'))->toBe($this->patient->id);
});

it('returns timeline of prescriptions', function () {
    Prescription::factory()->create([
        'user_id' => $this->doctor->id,
        'patient_id' => $this->patient->id,
        'room_id' => $this->room->id,
        'specialty_id' => $this->specialty->id,
        'created_at' => now(),
    ]);

    $response = $this->actingAs($this->doctor, 'sanctum')
        ->getJson('/api/statistics/timeline')
        ->assertOk()
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['period', 'total'],
            ],
        ]);

    expect($response->json('data'))->not->toBeEmpty();
});

it('lists laboratories and brands catalogs', function () {
    $this->actingAs($this->doctor, 'sanctum')
        ->getJson('/api/laboratories')
        ->assertOk()
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'name', 'country', 'brands'],
            ],
        ]);

    $this->actingAs($this->doctor, 'sanctum')
        ->getJson('/api/brands')
        ->assertOk()
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'name', 'laboratory'],
            ],
        ]);
});
