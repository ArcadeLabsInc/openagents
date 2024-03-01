<?php

use App\Models\Agent;
use App\Models\Message;
use App\Models\Thread;

it('has many messages', function () {
    $thread = Thread::factory()->create();
    $message = Message::factory()->create(['thread_id' => $thread->id]);

    $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $thread->messages);
    $this->assertInstanceOf(Message::class, $thread->messages->first());
});

it('has many agents', function () {
    $thread = Thread::factory()->create();
    $agent = Agent::factory()->create();

    $thread->agents()->attach($agent->id);

    $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $thread->agents);
    $this->assertInstanceOf(Agent::class, $thread->agents->first());
});

//it('must belong to an agent', function () {
//    $agent = Agent::factory()->create();
//    $conversation = Conversation::factory()->create([
//        'agent_id' => $agent->id,
//    ]);
//
//    $this->assertInstanceOf(Agent::class, $conversation->agent);
//
//    $this->expectException(QueryException::class);
//    $conversationNull = Conversation::factory()->create([
//        'agent_id' => null,
//    ]);
//});
