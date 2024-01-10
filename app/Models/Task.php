<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function steps()
    {
        return $this->hasMany(Step::class);
    }

    public function run($input)
    {
        // Load the first step
        $step = $this->steps()->first();

        // Create from it a TaskExecuted
        $task_executed = TaskExecuted::create([
            'task_id' => $this->id,
            // Current user ID if authed or null
            'user_id' => auth()->id(),
            'status' => 'pending'
        ]);

        $step_executed = null;
        $steps = $this->steps()->get();
        // Loop through all the task's steps, passing the output of each to the next
        foreach ($steps as $step) {
            if ($step->order !== 1) {
                $input = $prev_step_executed->output;
            }
            // Create a new StepExecuted with this step and task_executed
            $step_executed = StepExecuted::create([
                'step_id' => $step->id,
                'input' => json_encode($input),
                'order' => $step->order,
                'task_executed_id' => $task_executed->id,
                'user_id' => auth()->id(),
                'status' => 'pending',
            ]);
            $step_executed->output = $step_executed->run();
            $step_executed->save();

            $prev_step_executed = $step_executed;
        }

        // if $step_executed is null, then there are no steps
        if (!$step_executed) {
            return null;
        }

        // Return the output of the final StepExecuted
        return $step_executed->fresh()->output;
    }
}
