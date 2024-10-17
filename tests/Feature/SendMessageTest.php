<?php

use App\Models\User;
use App\Models\Thread;

test('authenticated user can send a message', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/send-message', [
            'message' => 'Test message'
        ]);

    $response->assertStatus(302);
    $response->assertSessionHas('success', 'Message sent successfully!');

    $this->assertDatabaseHas('messages', [
        'user_id' => $user->id,
        'content' => 'Test message'
    ]);

    $this->assertDatabaseHas('threads', [
        'user_id' => $user->id
    ]);
});

test('authenticated user can send a message to an existing thread', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->post('/send-message', [
            'message' => 'Test message',
            'thread_id' => $thread->id
        ]);

    $response->assertStatus(302);
    $response->assertSessionHas('success', 'Message sent successfully!');

    $this->assertDatabaseHas('messages', [
        'user_id' => $user->id,
        'thread_id' => $thread->id,
        'content' => 'Test message'
    ]);
});

test('unauthenticated user cannot send a message', function () {
    $response = $this->post('/send-message', [
        'message' => 'Test message'
    ]);

    $response->assertStatus(302);
    $response->assertRedirect('/login');
});

test('message cannot be empty', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/send-message', [
            'message' => ''
        ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('message');
});
