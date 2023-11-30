<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Medicament;
use Laravel\Sanctum\Sanctum;

class MedicamentTest extends TestCase
{
  use WithFaker;

  private $user;

  public function setUp(): void
  {
    parent::setUp();
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user, ['*']);

  }
  public function test_insert(): void
  {
    $response = $this->post('api/medicament', [
      "name" => fake()->name(),
      "ingredient" => fake()->word(),
      "way" => fake()->word(),
      "form" => fake()->word(),
    ]);

    $response->assertOk();
  }
  public function test_update(): void
  {
    $medicament = Medicament::factory()->create();
    $response = $this->put('api/medicament/' . $medicament->id, [
      "name" => fake()->name(),
      "ingredient" => fake()->word(),
      "way" => fake()->word(),
      "form" => fake()->word(),
    ]);

    $response->assertOk();
  }
  public function test_show(): void
  {
    $medicament = Medicament::factory()->create();
    $response = $this->get('api/medicament/' . $medicament->id);

    $response->assertOk();
  }
  public function test_list(): void
  {
    Medicament::factory(10)->create();
    $response = $this->get('api/medicament');

    $response->assertOk();
    $this->assertCount(10, $response->json()['data']);
  }
}
