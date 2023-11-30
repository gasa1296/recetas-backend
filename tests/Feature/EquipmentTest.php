<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Equipment;
use Laravel\Sanctum\Sanctum;

class EquipmentTest extends TestCase
{
  use WithFaker, RefreshDatabase;

  private $user;

  public function setUp(): void
  {
    parent::setUp();
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user, ['*']);

  }
  public function test_insert(): void
  {
    $response = $this->post('api/equipment', [
      "name" => fake()->word(),
      "image" => fake()->imageUrl(),
    ]);

    $response->assertOk();
  }
  public function test_update(): void
  {
    $equipment = Equipment::factory()->create();
    $response = $this->put('api/equipment/' . $equipment->id, [
      "name" => fake()->word(),
      "image" => fake()->imageUrl(),
    ]);

    $response->assertOk();
  }
  public function test_show(): void
  {
    $equipment = Equipment::factory()->create();
    $response = $this->get('api/equipment/' . $equipment->id);

    $response->assertOk();
  }
  public function test_list(): void
  {
    Equipment::factory(10)->create();
    $response = $this->get('api/equipment');

    $response->assertOk();
    $this->assertCount(10, $response->json()['data']);
  }
}
