<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
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
            "fist_name" => fake()->firstName(),
            "last_name1" => fake()->lastName(),
            "last_name2" => fake()->lastName(),
            "email" => fake()->email(),
            "phone1" => fake()->phoneNumber(),
            "phone2" => fake()->phoneNumber(),
            "birth_date" => fake()->date(),
            'genre' => fake()->randomElement(['M', 'F']),
        ]);

        $response->assertOk();
    }
    public function test_update(): void
    {
        $patient = Patient::factory()->create();
        $response = $this->put('api/patient/' . $patient->id, [
            "fist_name" => fake()->firstName(),
            "last_name1" => fake()->lastName(),
            "last_name2" => fake()->lastName(),
            "email" => fake()->email(),
            "phone1" => fake()->phoneNumber(),
            "phone2" => fake()->phoneNumber(),
            "birth_date" => fake()->date(),
            'genre' => fake()->randomElement(['M', 'F']),
        ]);

        $response->assertOk();
    }
    public function test_show(): void
    {
        $patient = Patient::factory()->create();
        $response = $this->get('api/patient/' . $patient->id);

        $response->assertOk();
    }
    public function test_list(): void
    {
        Patient::factory(10)->create();
        $response = $this->get('api/patient');

        $response->assertOk();
        $this->assertCount(10, $response->json()['data']);
    }
}
