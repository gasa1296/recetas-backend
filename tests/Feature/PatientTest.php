<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\Prescription;
use Laravel\Sanctum\Sanctum;

class PatientTest extends TestCase
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
        $response = $this->post('api/patient', [
            "first_name" => fake()->firstName(),
            "last_name1" => fake()->lastName(),
            "last_name2" => fake()->lastName(),
            "email" => fake()->email(),
            "phone1" => fake()->phoneNumber(),
            "phone2" => fake()->phoneNumber(),
            "birth_date" => fake()->date(),
            'gender' => fake()->randomElement(['M', 'F']),
        ]);

        $response->assertStatus(201);
    }
    public function test_update(): void
    {
        $instance = Patient::factory()->create(['user_id' => $this->user->id]);
        $response = $this->put('api/patient/' . $instance->id, [
            "first_name" => fake()->firstName(),
            "last_name1" => fake()->lastName(),
            "last_name2" => fake()->lastName(),
            "email" => fake()->email(),
            "phone1" => fake()->phoneNumber(),
            "phone2" => fake()->phoneNumber(),
            "birth_date" => fake()->date(),
            'gender' => fake()->randomElement(['M', 'F']),
        ]);
        $response->assertOk();
    }
    public function test_show(): void
    {
        $instance = Patient::factory()
            ->has(Prescription::factory()->count(3), 'prescriptions')
            ->create(['user_id' => $this->user->id]);
        $response = $this->get('api/patient/' . $instance->id);
        $response->assertOk();
        $this->assertCount(3, $response->json()['data']['prescriptions']);
    }
    public function test_list(): void
    {
        Patient::factory(10)
            ->has(Prescription::factory()->count(10), 'prescriptions')
            ->create(['user_id' => $this->user->id]);
        $response = $this->get('api/patient');
        $response->assertOk();
        $this->assertCount(10, $response->json()['data']);
    }
    public function test_search(): void
    {
        Patient::factory(100)->create(['user_id' => $this->user->id]);
        $response = $this->get('api/patient?search=F');
        $response->assertOk();
        $this->assertGreaterThan(1, $response->json()['data']);
    }
}
