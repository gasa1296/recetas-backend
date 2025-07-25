<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

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
        $response = $this->post('api/auth/recregf2', [
            'first_name' => fake()->firstName(),
            'last_name1' => fake()->lastName(),
            'last_name2' => fake()->lastName(),
            'gender' => fake()->randomElement(['M','F']),
            'fesa' => "MED00040",
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'phone1' => json_encode([
                ['phone' => '0123456789']
            ]),
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
                    'design' => fake()->randomElement([env('F1'), env('F2'), env('F3')]),
                ]
            ],
            'specializations' => [
                [
                    'name' => fake()->words(3, true),
                    'identification' => fake()->unique()->words(3, true),
                    'university' => fake()->words(3, true),
                ]
            ],
            'logo_room' => [UploadedFile::fake()->image('photo.png')],
            'logo_spec' => [UploadedFile::fake()->image('photo.png')],
        ]);
        $response->assertOk();
    }
    public function test_failRegisterByFesa(): void
    {
        $response = $this->post('api/auth/recregf2', [
            'first_name' => fake()->firstName(),
            'last_name1' => fake()->lastName(),
            'last_name2' => fake()->lastName(),
            'phone1' => fake()->phoneNumber(),
            'phone2' => fake()->phoneNumber(),
            'gender' => fake()->randomElement(['M', 'F']),
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
                    'design' => fake()->randomElement([env('F1'), env('F2'), env('F3')]),
                ]
            ],
            'specializations' => [
                [
                    'name' => fake()->words(3, true),
                    'identification' => fake()->unique()->words(3, true),
                    'university' => fake()->words(3, true),
                ]
            ],
            'logo_room' => [UploadedFile::fake()->image('photo.png')],
            'logo_spec' => [UploadedFile::fake()->image('photo.png')],
        ]);
        $response->assertStatus(400);
    }
    public function test_logout(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        $response = $this->delete('api/auth/logout');
        $response->assertOk();
    }
}
