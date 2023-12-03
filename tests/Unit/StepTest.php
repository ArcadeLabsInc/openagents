<?php

use App\Models\Agent;
use App\Models\Step;
use App\Models\Task;

it('has a description', function () {
    $step = Step::factory()->create([
        'input' => null,
        'output' => null
    ]);
    expect($step->description)->toBeNull();

    $step = Step::factory()->create([
        'input' => null,
        'output' => null,
        'description' => 'foo'
    ]);
    expect($step->description)->toBe('foo');
});

it('belongs to an agent', function () {
    $step = Step::factory()->create();
    expect($step->agent)->toBeInstanceOf(Agent::class);
});

it('belongs to a task', function () {
    $step = Step::factory()->create();
    expect($step->task)->toBeInstanceOf(Task::class);
});

it('has input and output fields', function () {
    $step = Step::factory()->create([
        'input' => null,
        'output' => null
    ]);
    expect($step->input)->toBeNull();
    expect($step->output)->toBeNull();

    $input = ['foo' => 'bar'];
    $output = ['result' => 'success'];

    $step = Step::factory()->create([
      'input' => json_encode($input),
      'output' => json_encode($output)
    ]);

    expect($step->input)->toBe(json_encode($input));
    expect($step->output)->toBe(json_encode($output));
});

it('has many artifacts', function () {
    $step = Step::factory()->create();
    $step->artifacts()->create([
      'name' => 'foo',
      'path' => 'bar',
      'agent_id' => $step->agent->id,
    ]);
    $step->artifacts()->create([
      'name' => 'baz',
      'path' => 'qux',
      'agent_id' => $step->agent->id,
    ]);
    expect($step->artifacts)->toHaveCount(2);
});
