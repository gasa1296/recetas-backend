<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class ProfileTest extends TestCase
{
    use WithFaker;
    private $token;

    public function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $response = $this->post('api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $this->token = $response->json()['token'];
        
    }
    
    public function test_show(): void
    {
        $response = $this->get('api/profile', headers: [
            'Authorization' => 'Bearer ' . $this->token,
        ]);
        $response->assertOk();
    }
    public function test_not_update(): void
    {
        $response = $this->put('api/profile', headers: [
            'Authorization' => 'Bearer ' . $this->token,
        ]);
        $response->assertBadRequest();
    }
    public function test_delete(): void
    {
        $response = $this->delete('api/profile', headers: [
            'Authorization' => 'Bearer ' . $this->token,
        ]);
        $response->assertOk();
    }
}
