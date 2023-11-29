<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use WithFaker;

    public function test_login(): void
    {
        $user = User::factory()->create();

        $response = $this->post('api/auth/login', [
            'email'=> $user->email,
            'password'=> 'password',
        ]);
        $response->assertOk();
    }
    public function test_register(): void
    {
        $response = $this->post('api/auth/register', [
            'first_name' => fake()->firstName(),
            'last_name1' => fake()->lastName(),
            'last_name2' => fake()->lastName(),
            'identification' => fake()->randomNumber(),
            'especialization' => fake()->jobTitle(),
            'phone1' => fake()->phoneNumber(),
            'phone2' => fake()->phoneNumber(),
            'genre' => fake()->randomElement(['M','F']),
            'university' => fake()->name(),
            'fesa' => fake()->randomNumber(),
            'image' => fake()->imageUrl(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
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
            'logo' => fake()->imageUrl(),
            'design' => fake()->randomElement([1, 2, 3]),
        ]);
        $response->assertOk();
    }
    public function test_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->post('api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $token = $response->json()['token'];
        $response = $this->delete('api/auth/logout',headers: [
            'Authorization'=> 'Bearer ' . $token,
        ]);
        $response->assertOk();
    }
}
