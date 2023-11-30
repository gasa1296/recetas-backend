<?php

namespace Tests\Feature;

use App\Models\ConsultingRoom;
use App\Models\PrescriptionMedicament;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Prescription;
use App\Models\Medicament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class PrescriptionMedicamentTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    private $user;
    private $room;
    private $prescription;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->room = ConsultingRoom::factory()->create(["user_id" => $this->user]);
        $this->prescription = Prescription::factory()->create([
            'user_id' => $this->user->id,
            'room_id' => $this->room->id,
        ]);
        Sanctum::actingAs($this->user, ['*']);

    }
    public function test_insert(): void
    {
        $instance = Prescription::factory()->create([
            'user_id' => $this->user->id,
            'room_id' => $this->room->id,
        ]);
        $response = $this->post("api/prescription/$instance->id/medicament", [
            'dose' => fake()->randomDigit(),
            'way' => fake()->words(10, true),
            'frequency' => fake()->randomNumber(),
            'duration' => fake()->randomNumber(),
            'medicament_id' => Medicament::factory()->create()->id,
        ]);
        $response->assertOk();
        $response = $this->post("api/prescription/$instance->id/medicament?bulk=1", [
            [
                'dose' => fake()->randomDigit(),
                'way' => fake()->words(10, true),
                'frequency' => fake()->randomNumber(),
                'duration' => fake()->randomNumber(),
                'medicament_id' => Medicament::factory()->create()->id,
            ],
            [
                'dose' => fake()->randomDigit(),
                'way' => fake()->words(10, true),
                'frequency' => fake()->randomNumber(),
                'duration' => fake()->randomNumber(),
                'medicament_id' => Medicament::factory()->create()->id,
            ],
        ]);
        //print_r($response->json());
        $response->assertOk();
        $this->assertCount(2, $response->json());
    }
    public function test_update(): void
    {
        $instance = PrescriptionMedicament::factory()->create([
            'prescription_id' => $this->prescription->id,
        ]);
        $response = $this->put("api/prescription/$instance->prescription_id/medicament/$instance->medicament_id", [
            'dose' => fake()->randomDigit(),
            'way' => fake()->words(10, true),
            'frequency' => fake()->randomNumber(),
            'duration' => fake()->randomNumber(),
        ]);
        $response->assertOk();
    }
    public function test_show(): void
    {
        $instance = PrescriptionMedicament::factory()->create([
            'prescription_id' => $this->prescription->id,
        ]);
        $response = $this->get("api/prescription/$instance->prescription_id/medicament/$instance->medicament_id");

        $response->assertOk();
    }
    public function test_list(): void
    {
        PrescriptionMedicament::factory(10)->create([
            'prescription_id' => $this->prescription->id,
        ]);
        $prescription_id = $this->prescription->id;
        $response = $this->get("api/prescription/$prescription_id/medicament");

        $response->assertOk();
        $this->assertCount(10, $response->json()['data']);
    }
    public function test_delete(): void
    {
        $instance = PrescriptionMedicament::factory()->create([
            'prescription_id' => $this->prescription->id,
        ]);
        $response = $this->delete("api/prescription/$instance->prescription_id/medicament/$instance->medicament_id");

        $response->assertOk();
    }
}
