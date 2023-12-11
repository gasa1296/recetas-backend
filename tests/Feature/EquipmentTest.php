<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Equipment;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;

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
      'image' => UploadedFile::fake()->image('photo.jpg'),
    ]);

    $response->assertOk();
  }
  public function test_update(): void
  {
    $instance = Equipment::factory()->create();
    $response = $this->put('api/equipment/' . $instance->id, [
      "name" => fake()->word(),
      'image' => UploadedFile::fake()->image('photo.jpg'),
    ]);

    $response->assertOk();
  }
  public function test_show(): void
  {
    $instance = Equipment::factory()->create();
    $response = $this->get('api/equipment/' . $instance->id);

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
