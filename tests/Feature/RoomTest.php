<?php

namespace Tests\Feature;

use App\Models\ConsultingRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoomTest extends TestCase
{
    use WithFaker;

    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

    }

    public function test_upsert(): void
    {
        $instance = ConsultingRoom::factory()->create(['user_id' => $this->user]);
        $response = $this->post('api/room', [
            'data' => [
                [
                    'name' => fake()->name(),
                    'zip' => fake()->postcode(),
                    'street' => fake()->streetAddress(),
                    'colony' => fake()->city(),
                    'state' => fake()->city(),
                    'delegation' => fake()->city(),
                    'n_exterior' => fake()->randomNumber(),
                    'n_interior' => fake()->randomNumber(),
                    'address' => fake()->address(),
                    'phone' => fake()->phoneNumber(),
                    'design' => fake()->randomElement([env('F1'), env('F2'), env('F3')]),
                ],
                [
                    'id' => $instance->id,
                    'name' => fake()->name(),
                    'zip' => fake()->postcode(),
                    'street' => fake()->streetAddress(),
                    'colony' => fake()->city(),
                    'state' => fake()->city(),
                    'delegation' => fake()->city(),
                    'n_exterior' => fake()->randomNumber(),
                    'n_interior' => fake()->randomNumber(),
                    'address' => fake()->address(),
                    'phone' => fake()->phoneNumber(),
                    'design' => fake()->randomElement([env('F1'), env('F2'), env('F3')]),
                ],
            ],
            'logo' => [
                UploadedFile::fake()->image('photo.png'),
                UploadedFile::fake()->image('photo.png'),
            ],
        ]);

        $response->assertOk();
    }

    public function test_update(): void
    {
        $instance = ConsultingRoom::factory()->create(['user_id' => $this->user]);
        $response = $this->put('api/room/'.$instance->id, [
            'name' => fake()->name(),
            'zip' => fake()->postcode(),
            'street' => fake()->streetAddress(),
            'colony' => fake()->city(),
            'state' => fake()->city(),
            'delegation' => fake()->city(),
            'n_exterior' => fake()->randomNumber(),
            'n_interior' => fake()->randomNumber(),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'logo' => UploadedFile::fake()->image('photo.png'),
            'design' => fake()->randomElement([env('F1'), env('F2'), env('F3')]),
        ]);

        $response->assertOk();
    }

    public function test_show(): void
    {
        $instance = ConsultingRoom::factory()->create(['user_id' => $this->user]);
        $response = $this->get('api/room/'.$instance->id);

        $response->assertOk();
    }

    public function test_empty_list(): void
    {
        ConsultingRoom::factory(1)->create();
        $response = $this->get('api/room');
        $response->assertOk();
        $this->assertEmpty($response->json()['data']);
    }

    public function test_list(): void
    {
        ConsultingRoom::factory(11)->create(['user_id' => $this->user]);
        $response = $this->get('api/room');

        $response->assertOk();
        $this->assertCount(10, $response->json()['data']);
    }
}
