<?php

namespace Tests\Feature;

use App\Models\ConsultingRoom;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PrescriptionTest extends TestCase
{
    use WithFaker;

    private $user;

    private $room;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()
            ->has(ConsultingRoom::factory()->count(1), 'rooms')
            ->has(Specialization::factory()->count(1), 'specializations')
            ->create();
        Sanctum::actingAs($this->user, ['*']);

    }

    public function test_insert(): void
    {
        $response = $this->post('api/prescription', [
            'temp' => fake()->randomFloat(2, 20, 40),
            'weight' => fake()->randomFloat(2, 30, 300),
            'height' => fake()->randomFloat(2, 30, 300),
            'pressure' => fake()->randomNumber().' '.fake()->randomNumber(),
            'saturation' => fake()->randomFloat(2, 1, 100),
            'ppm' => fake()->randomFloat(2, 1, 100),
            'allergy' => fake()->words(10, true),
            'add_med' => json_encode([
                [
                    'name' => fake()->words(10, true),
                    'indications' => fake()->words(10, true),
                ],
                [
                    'name' => fake()->words(10, true),
                    'indications' => fake()->words(10, true),
                ],
                [
                    'name' => fake()->words(10, true),
                    'indications' => fake()->words(10, true),
                ],
                [
                    'name' => fake()->words(10, true),
                    'indications' => fake()->words(10, true),
                ]]),
            'diagnostic' => fake()->words(10, true),
            'diet' => fake()->words(10, true),
            'add' => fake()->words(10, true),
            'user_id' => $this->user->id,
            'room_id' => $this->user->rooms[0]->id,
            'patient_id' => Patient::factory()->create()->id,
            'file' => UploadedFile::fake()->image('photo.png'),
            'status' => fake()->randomNumber(3),
            'medicaments' => [
                [
                    'dose' => fake()->randomDigit().fake()->word(),
                    'way' => fake()->words(10, true),
                    'frequency' => fake()->randomNumber().fake()->word(),
                    'duration' => fake()->randomNumber().fake()->word(),
                    'quantity' => fake()->randomDigit(),
                    'name' => fake()->word(),
                    'type' => fake()->word(),
                    'family' => fake()->word(),
                    'group' => fake()->word(),
                    'salt' => fake()->word(),
                    'medicament_id' => fake()->randomNumber(),
                ],
                [
                    'dose' => fake()->randomDigit().fake()->word(),
                    'way' => fake()->words(10, true),
                    'frequency' => fake()->randomNumber().fake()->word(),
                    'duration' => fake()->randomNumber().fake()->word(),
                    'quantity' => fake()->randomDigit(),
                    'name' => fake()->word(),
                    'type' => fake()->word(),
                    'family' => fake()->word(),
                    'group' => fake()->word(),
                    'salt' => fake()->word(),
                    'medicament_id' => fake()->randomNumber(),
                ],
                [
                    'dose' => fake()->randomDigit().fake()->word(),
                    'way' => fake()->words(10, true),
                    'frequency' => fake()->randomNumber().fake()->word(),
                    'duration' => fake()->randomNumber().fake()->word(),
                    'quantity' => fake()->randomDigit(),
                    'name' => fake()->word(),
                    'type' => fake()->word(),
                    'family' => fake()->word(),
                    'group' => fake()->word(),
                    'salt' => fake()->word(),
                    'medicament_id' => fake()->randomNumber(),
                ],
                [
                    'dose' => fake()->randomDigit().fake()->word(),
                    'way' => fake()->words(10, true),
                    'frequency' => fake()->randomNumber().fake()->word(),
                    'duration' => fake()->randomNumber().fake()->word(),
                    'quantity' => fake()->randomDigit(),
                    'name' => fake()->word(),
                    'type' => fake()->word(),
                    'family' => fake()->word(),
                    'group' => fake()->word(),
                    'salt' => fake()->word(),
                    'medicament_id' => fake()->randomNumber(),
                ],
            ],
        ]);
        $response->assertStatus(201);
    }

    public function test_update(): void
    {
        $instance = Prescription::factory()->create([
            'user_id' => $this->user,
            'room_id' => $this->user->rooms[0]->id,
        ]);
        $response = $this->put('api/prescription/'.$instance->id, [
            'temp' => fake()->randomFloat(2, 20, 40),
            'weight' => fake()->randomFloat(2, 30, 300),
            'height' => fake()->randomFloat(2, 30, 300),
            'pressure' => fake()->randomNumber().' '.fake()->randomNumber(),
            'saturation' => fake()->randomFloat(2, 1, 100),
            'ppm' => fake()->randomFloat(2, 1, 100),
            'allergy' => fake()->words(10, true),
            'diagnostic' => fake()->words(10, true),
            'diet' => fake()->words(10, true),
            'add' => fake()->words(10, true),
            'user_id' => $this->user->id,
            'room_id' => $this->user->rooms[0]->id,
            'patient_id' => Patient::factory()->create()->id,
            'file' => UploadedFile::fake()->image('photo.png'),
            'status' => fake()->randomNumber(3),
        ]);

        $response->assertOk();
    }

    public function test_show(): void
    {
        $instance = Prescription::factory()->create([
            'user_id' => $this->user,
            'room_id' => $this->user->rooms[0]->id,
        ]);
        $response = $this->get('api/prescription/'.$instance->id);

        $response->assertOk();
    }

    public function test_list(): void
    {
        Prescription::factory(11)->create([
            'user_id' => $this->user,
            'room_id' => $this->user->rooms[0]->id,
        ]);
        $response = $this->get('api/prescription');
        $response->assertOk();
        $this->assertCount(10, $response->json()['data']);
    }
}
