<?php

use App\Models\Thread;
use App\Models\User;

test('visiting /chat redirects to users first thread', function () {
    $user = User::factory()->create();
    $thread1 = Thread::factory()->create(['user_id' => $user->id]);
    $thread2 = Thread::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get('/chat');
    $response->assertRedirect("/chat/{$thread1->id}");
});

test('visiting /chat redirects to /chat/create when user has no threads', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/chat');
    $response->assertRedirect('/chat/create');
});

test('visiting /chat/create creates a new thread', function () {
    $user = User::factory()->create();
    
    $this->assertDatabaseCount('threads', 0);
    
    $response = $this->actingAs($user)->get('/chat/create');
    
    $this->assertDatabaseCount('threads', 1);
    $thread = Thread::first();
    $response->assertRedirect("/chat/{$thread->id}");
});