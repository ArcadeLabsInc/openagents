<?php

use App\Models\Agent;
use App\Models\Conversation;
use App\Models\Step;
use App\Models\Task;
use App\Models\Thought;
use App\Models\User;

it('belongs to a user', function () {
    $user = User::factory()->create();
    $agent = Agent::factory()->create(['user_id' => $user->id]);

    $this->assertInstanceOf(User::class, $agent->user);
});

it('has a name', function () {
    $agent = Agent::factory()->create(['name' => 'My Agent']);
    expect($agent->name)->toBe('My Agent');
});

it('has many conversations', function () {
    $user = User::factory()->create();
    $agent = Agent::factory()->create(['user_id' => $user->id]);
    $conversation = Conversation::factory()->create([
      'agent_id' => $agent->id,
      'user_id' => $user->id
    ]);

    $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $agent->conversations);
    $this->assertInstanceOf(Conversation::class, $agent->conversations->first());
});

it('has many tasks', function () {
    $agent = Agent::factory()->create();
    $task = Task::factory()->create(['agent_id' => $agent->id]);

    $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $agent->tasks);
    $this->assertInstanceOf(Task::class, $agent->tasks->first());
});

it('has many steps', function () {
    $agent = Agent::factory()->create();
    $step = Step::factory()->create(['agent_id' => $agent->id]);

    $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $agent->steps);
    $this->assertInstanceOf(Step::class, $agent->steps->first());
});

it('has thoughts', function () {
    $agent = Agent::factory()->create();
    $thought = Thought::factory()->create(['agent_id' => $agent->id]);

    $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $agent->thoughts);
    $this->assertInstanceOf(Thought::class, $agent->thoughts->first());
});
