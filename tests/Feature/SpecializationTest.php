<?php

namespace Tests\Feature;

use App\Models\Specialization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class SpecializationTest extends TestCase
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
        $response = $this->post('api/specialization', [
            "name" => fake()->words(3, true),
            "identification" => fake()->unique()->words(3, true),
            "university" => fake()->words(3, true),
            "logo" => fake()->imageUrl(),
        ]);

        $response->assertOk();
    }
    public function test_update(): void
    {
        $instance = Specialization::factory()->create(["user_id" => $this->user]);
        $response = $this->put('api/specialization/' . $instance->id, [
            "name" => fake()->words(3, true),
            "identification" => fake()->unique()->words(3, true),
            "university" => fake()->words(3, true),
            "logo" => fake()->imageUrl(),
        ]);

        $response->assertOk();
    }
    public function test_show(): void
    {
        $instance = Specialization::factory()->create(["user_id" => $this->user]);
        $response = $this->get('api/specialization/' . $instance->id);

        $response->assertOk();
    }
    public function test_empty_list(): void
    {
        Specialization::factory(10)->create();
        $response = $this->get('api/specialization');
        $response->assertOk();
        $this->assertEmpty($response->json()['data']);
    }
    public function test_list(): void
    {
        Specialization::factory(10)->create(["user_id" => $this->user]);
        $response = $this->get('api/specialization');

        $response->assertOk();
        $this->assertCount(10, $response->json()['data']);
    }
}
