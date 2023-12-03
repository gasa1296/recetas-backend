<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    use WithFaker, RefreshDatabase;

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
            'phone1' => fake()->phoneNumber(),
            'phone2' => fake()->phoneNumber(),
            'gender' => fake()->randomElement(['M','F']),
            'fesa' => fake()->randomNumber(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'rooms' => [
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
                    'logo' => fake()->imageUrl(),
                    'design' => fake()->randomElement([1, 2, 3]),
                ]
            ],
        ]);
        print_r($response->json());
        $response->assertOk();
    }
    public function test_logout(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        $response = $this->delete('api/auth/logout');
        $response->assertOk();
    }
}
