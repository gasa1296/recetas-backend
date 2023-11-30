<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\ConsultingRoom;
use Laravel\Sanctum\Sanctum;

class RoomTest extends TestCase
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
        $response = $this->post('api/room', [
            "name"=> fake()->name(),
            "zip" => fake()->postcode(),
            "street" => fake()->streetAddress(),
            "colony" => fake()->city(),
            "state" => fake()->city(),
            "delegation" => fake()->city(),
            "n_exterior" => fake()->randomNumber(),
            "n_interior" => fake()->randomNumber(),
            "address" => fake()->address(),
            "phone" => fake()->phoneNumber(),
            "logo" => fake()->imageUrl(),
            "design" => fake()->randomElement([1,2,3]),
        ]);

        $response->assertOk();
    }
    public function test_update(): void
    {
        $room = ConsultingRoom::factory()->create(["user_id" => $this->user]);
        $response = $this->put('api/room/' . $room->id, [
            "name" => fake()->name(),
            "zip" => fake()->postcode(),
            "street" => fake()->streetAddress(),
            "colony" => fake()->city(),
            "state" => fake()->city(),
            "delegation" => fake()->city(),
            "n_exterior" => fake()->randomNumber(),
            "n_interior" => fake()->randomNumber(),
            "address" => fake()->address(),
            "phone" => fake()->phoneNumber(),
            "logo" => fake()->imageUrl(),
            "design" => fake()->randomElement([1, 2, 3]),
        ]);

        $response->assertOk();
    }
    public function test_show(): void
    {
        $room = ConsultingRoom::factory()->create(["user_id" => $this->user]);
        $response = $this->get('api/room/' . $room->id);

        $response->assertOk();
    }
    public function test_empty_list(): void
    {
        ConsultingRoom::factory(10)->create();
        $response = $this->get('api/room');
        $response->assertOk();
        $this->assertEmpty($response->json()['data']);
    }
    public function test_list(): void
    {
        ConsultingRoom::factory(10)->create(["user_id" => $this->user]);
        $response = $this->get('api/room');

        $response->assertOk();
        $this->assertCount(10, $response->json()['data']);
    }
}
