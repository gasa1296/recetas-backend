<?php

namespace Tests\Feature;

use App\Models\Specialization;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SpecializationTest extends TestCase
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
        $instance = Specialization::factory()->create(['user_id' => $this->user]);
        $response = $this->post('api/specialization', [
            'data' => [
                [
                    'name' => fake()->words(3, true),
                    'identification' => fake()->unique()->words(3, true),
                    'university' => fake()->words(3, true),
                ],
                [
                    'id' => $instance->id,
                    'name' => $instance->name,
                    'identification' => fake()->unique()->words(3, true),
                    'university' => $instance->university,
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
        $instance = Specialization::factory()->create(['user_id' => $this->user]);
        $response = $this->put('api/specialization/'.$instance->id, [
            'name' => fake()->words(3, true),
            'identification' => fake()->unique()->words(3, true),
            'university' => fake()->words(3, true),
            'logo' => UploadedFile::fake()->image('photo.png'),
        ]);

        $response->assertOk();
    }

    public function test_show(): void
    {
        $instance = Specialization::factory()->create(['user_id' => $this->user]);
        $response = $this->get('api/specialization/'.$instance->id);

        $response->assertOk();
    }

    public function test_empty_list(): void
    {
        Specialization::factory(2)->create();
        $response = $this->get('api/specialization');
        $response->assertOk();
        $this->assertEmpty($response->json()['data']);
    }

    public function test_list(): void
    {
        Specialization::factory(11)->create(['user_id' => $this->user]);
        $response = $this->get('api/specialization');

        $response->assertOk();
        $this->assertCount(10, $response->json()['data']);
    }
}
