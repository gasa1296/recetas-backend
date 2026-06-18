<?php

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| RoomController
|--------------------------------------------------------------------------
| GET    /api/rooms        -> auth:sanctum
| POST   /api/rooms        -> auth:sanctum (RoomRequest)
| GET    /api/rooms/{id}   -> auth:sanctum
| PUT    /api/rooms/{id}   -> auth:sanctum (RoomRequest)
| DELETE /api/rooms/{id}   -> auth:sanctum
*/

dataset('invalid_room_payloads', [
    'missing name' => [
        ['zip' => '00000', 'address' => 'St 1', 'phone' => ['+1']],
        ['name'],
    ],
    'missing zip' => [
        ['name' => 'Clinic A', 'address' => 'St 1', 'phone' => ['+1']],
        ['zip'],
    ],
    'missing address' => [
        ['name' => 'Clinic A', 'zip' => '00000', 'phone' => ['+1']],
        ['address'],
    ],
    'missing phone' => [
        ['name' => 'Clinic A', 'zip' => '00000', 'address' => 'St 1'],
        ['phone'],
    ],
    'phone not array' => [
        ['name' => 'Clinic A', 'zip' => '00000', 'address' => 'St 1', 'phone' => 'string'],
        ['phone'],
    ],
]);

test('rooms index requires authentication', function () {
    $response = $this->getJson('/api/rooms');

    $response->assertStatus(401);
});

test('rooms index returns only the authenticated user rooms', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    Room::factory()->count(3)->for($user)->create();
    Room::factory()->count(2)->for($other)->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/rooms');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'name', 'zip', 'address', 'phone'],
            ],
        ])
        ->assertJsonCount(3, 'data');
});

test('rooms store requires authentication', function () {
    $payload = [
        'name' => 'Clinic A',
        'zip' => '00000',
        'address' => 'St 1',
        'phone' => ['+1'],
    ];

    $response = $this->postJson('/api/rooms', $payload);

    $response->assertStatus(401);
});

test('rooms store rejects invalid request structure', function (array $payload, array $errors) {
    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/rooms', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors($errors);
})->with('invalid_room_payloads');

test('rooms store creates a room with valid request structure', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/rooms', [
            'name' => 'Clinic A',
            'zip' => '00000',
            'address' => '123 Main St',
            'phone' => ['+123456789'],
        ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['id', 'name', 'zip', 'address', 'phone'],
        ])
        ->assertJsonPath('data.name', 'Clinic A')
        ->assertJsonPath('data.zip', '00000')
        ->assertJsonPath('data.address', '123 Main St');

    $this->assertDatabaseHas('rooms', [
        'user_id' => $user->id,
        'name' => 'Clinic A',
        'zip' => '00000',
        'address' => '123 Main St',
    ]);
});

test('rooms show requires authentication', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create();

    $response = $this->getJson("/api/rooms/{$room->id}");

    $response->assertStatus(401);
});

test('rooms show returns 404 for room not owned by user', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $room = Room::factory()->for($owner)->create();

    $response = $this->actingAs($stranger, 'sanctum')
        ->getJson("/api/rooms/{$room->id}");

    $response->assertNotFound();
});

test('rooms show returns the requested room', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create([
        'name' => 'Clinic X',
        'zip' => '11111',
        'address' => '456 Side St',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/rooms/{$room->id}");

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['id', 'name', 'zip', 'address', 'phone'],
        ])
        ->assertJsonPath('data.id', $room->id)
        ->assertJsonPath('data.name', 'Clinic X')
        ->assertJsonPath('data.zip', '11111')
        ->assertJsonPath('data.address', '456 Side St');
});

test('rooms update requires authentication', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create(['name' => 'Old']);

    $response = $this->putJson("/api/rooms/{$room->id}", [
        'name' => 'X',
        'zip' => '0',
        'address' => 'Y',
        'phone' => ['+1'],
    ]);

    $response->assertStatus(401);
});

test('rooms update rejects invalid request structure', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create();

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/rooms/{$room->id}", [
            'name' => null,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('rooms update modifies the room with valid request structure', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create(['name' => 'Old']);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/rooms/{$room->id}", [
            'name' => 'New',
            'zip' => '99999',
            'address' => '789 New St',
            'phone' => ['+999'],
        ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.id', $room->id)
        ->assertJsonPath('data.name', 'New')
        ->assertJsonPath('data.address', '789 New St');

    $this->assertDatabaseHas('rooms', [
        'id' => $room->id,
        'name' => 'New',
    ]);
});

test('rooms destroy requires authentication', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create();

    $response = $this->deleteJson("/api/rooms/{$room->id}");

    $response->assertStatus(401);
});

test('rooms destroy soft deletes the room', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create();

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/rooms/{$room->id}");

    $response->assertSuccessful()
        ->assertJsonStructure(['success', 'message', 'data']);

    $this->assertSoftDeleted('rooms', [
        'id' => $room->id,
    ]);
});
