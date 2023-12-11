<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Medicament;
use Laravel\Sanctum\Sanctum;

class MedicamentTest extends TestCase
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
    $response = $this->post('api/medicament', [
      'name' => fake()->name(),
      'ingredient' => fake()->word(),
      'dose' => fake()->randomDigit(),
      'quantity' => fake()->randomDigit(),
    ]);

    $response->assertOk();
  }
  public function test_update(): void
  {
    $instance = Medicament::factory()->create();
    $response = $this->put('api/medicament/' . $instance->id, [
      'name' => fake()->name(),
      'ingredient' => fake()->word(),
      'dose' => fake()->randomDigit(),
      'quantity' => fake()->randomDigit(),
    ]);

    $response->assertOk();
  }
  public function test_show(): void
  {
    $instance = Medicament::factory()->create();
    $response = $this->get('api/medicament/' . $instance->id);

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
