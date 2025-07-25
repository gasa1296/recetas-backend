<?php

namespace Tests\Feature;

use App\Models\Prescription;
use App\Models\PrescriptionMedicament;
use Tests\TestCase;
class PrescriptionPublicTest extends TestCase
{
    private $prescription;

    public function setUp(): void
    {
        parent::setUp();
        $this->prescription = Prescription::factory()->create();

    }
    /**
     * A basic feature test example.
     */
    public function test_getById(): void
    {
        $response = $this->get('api/receta/' . $this->prescription->code, ['Authorization' => 'Bearer ' . env('PUBLIC_KEY', '')]);
        $response->assertStatus(200);
    }
    /**
     * A basic feature test example.
     */
    public function test_setClient(): void
    {
        $response = $this->post('api/receta/' . $this->prescription->code, [
            'client' => 1
        ], ['Authorization' => 'Bearer ' . env('PUBLIC_KEY', '')]);
        $response->assertStatus(200);
    }
    public function test_getByClient(): void
    {
        Prescription::factory(10)->create(['client' => 1]);
        $response = $this->get('api/receta/?client=1', ['Authorization' => 'Bearer ' . env('PUBLIC_KEY', '')]);
        $response->assertStatus(200);
    }
    public function test_updateStatus(): void
    {
        $elements = PrescriptionMedicament::factory(10)->create(['prescription_id' => $this->prescription->id, 'quantity' => 5]);
        $request = [];
        foreach ($elements as $el) {
            $request[$el->medicament_id] = ['total_exp' => 1];
        }
        $response = $this->put('api/receta/' . $this->prescription->code, $request, ['Authorization' => 'Bearer ' . env('PUBLIC_KEY', '')]);
        $this->assertEquals(1, $response->json()['data']['status']);
        $response->assertStatus(200);

        foreach ($elements as $el) {
            $request[$el->medicament_id] = ['total_exp' => 4];
        }
        $response = $this->put('api/receta/' . $this->prescription->code, $request, ['Authorization' => 'Bearer ' . env('PUBLIC_KEY', '')]);

        $this->assertEquals(1, $response->json()['data']['status']);
        $response->assertStatus(200);

        foreach ($elements as $el) {
            $request[$el->medicament_id] = ['total_exp' => 1];
        }
        $response = $this->put('api/receta/' . $this->prescription->code, $request, ['Authorization' => 'Bearer ' . env('PUBLIC_KEY', '')]);
        $response->assertStatus(200);
    }
}
