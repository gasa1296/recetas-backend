<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use WithFaker;

    private $token;

    public function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

    }

    public function test_show(): void
    {
        $response = $this->get('api/profile');
        $response->assertOk();
    }

    public function test_not_update(): void
    {
        $response = $this->put('api/profile');
        $response->assertBadRequest();
    }

    public function test_update(): void
    {

        $response = $this->put('api/profile', [
            'first_name' => fake()->firstName(),
            'last_name1' => fake()->lastName(),
            'last_name2' => fake()->lastName(),
            'phone1' => json_encode([
                ['phone' => '0123456789'],
            ]),
            'phone2' => fake()->phoneNumber(),
            'gender' => fake()->randomElement(['M', 'F']),
            'fesa' => fake()->randomNumber(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
        ]);
        $response->assertOk();
    }

    public function test_delete(): void
    {
        $response = $this->delete('api/profile');
        $response->assertOk();
    }
}
