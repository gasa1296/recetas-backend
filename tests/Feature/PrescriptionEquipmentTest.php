<?php

namespace Tests\Feature;

use App\Models\ConsultingRoom;
use App\Models\Equipment;
use App\Models\PrescriptionEquipment;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Prescription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class PrescriptionEquipmentTest extends TestCase
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
        $prescription = Prescription::factory()->create([
            'user_id' => $this->user->id,
            'room_id' => $this->room->id,
        ]);
        $response = $this->post("api/prescription/$prescription->id/equipment", [
            [
                'add' => fake()->words(10, true),
                'equipment_id' => Equipment::factory()->create()->id,
            ],
            [
                'add' => fake()->words(10, true),
                'equipment_id' => Equipment::factory()->create()->id,
            ],
        ]);
        $response->assertOk();
        $this->assertCount(2, $response->json());
    }
    public function test_update(): void
    {
        $prescriptionEquipment = PrescriptionEquipment::factory()->create([
            'prescription_id' => $this->prescription->id,
        ]);
        $prescription_id = $prescriptionEquipment->prescription_id;
        $equipment_id = $prescriptionEquipment->equipment_id;
        $response = $this->put("api/prescription/$prescription_id/equipment/$equipment_id", [
            "add"=> fake()->words(10, true),
        ]);
        $response->assertOk();
    }
    public function test_show(): void
    {
        $prescriptionEquipment = PrescriptionEquipment::factory()->create([
            'prescription_id' => $this->prescription->id,
        ]);
        $prescription_id = $prescriptionEquipment->prescription_id;
        $equipment_id = $prescriptionEquipment->equipment_id;
        $response = $this->get("api/prescription/$prescription_id/equipment/$equipment_id");

        $response->assertOk();
    }
    public function test_list(): void
    {
        PrescriptionEquipment::factory(10)->create([
            'prescription_id' => $this->prescription->id,
        ]);
        $prescription_id = $this->prescription->id;
        $response = $this->get("api/prescription/$prescription_id/equipment");

        $response->assertOk();
        $this->assertCount(10, $response->json()['data']);
    }
    public function test_delete(): void
    {
        $prescriptionEquipment = PrescriptionEquipment::factory()->create([
            'prescription_id' => $this->prescription->id,
        ]);
        $prescription_id = $prescriptionEquipment->prescription_id;
        $equipment_id = $prescriptionEquipment->equipment_id;
        $response = $this->delete("api/prescription/$prescription_id/equipment/$equipment_id");

        $response->assertOk();
    }
}
