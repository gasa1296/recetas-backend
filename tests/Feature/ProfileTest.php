<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Hash;

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
            'identification' => fake()->randomNumber(),
            'especialization' => fake()->jobTitle(),
            'phone1' => fake()->phoneNumber(),
            'phone2' => fake()->phoneNumber(),
            'genre' => fake()->randomElement(['M', 'F']),
            'university' => fake()->name(),
            'fesa' => fake()->randomNumber(),
            'image' => fake()->imageUrl(),
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
