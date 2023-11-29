<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

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
    public function test_delete(): void
    {
        $response = $this->delete('api/profile');
        $response->assertOk();
    }
}
