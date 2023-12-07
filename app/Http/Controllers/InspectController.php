<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Run;
use App\Models\Step;
use App\Models\Task;
use Inertia\Inertia;

class InspectController extends Controller
{
    public function index() {
        return Inertia::render('Inspect', [
            'agents' => Agent::all()->load('tasks'),
            'runs' => Run::all(),
            'steps' => Step::all(),
            'tasks' => Task::all(),
        ]);
    }

    public function showRun($id) {
        $run = Run::findOrFail($id)->load('steps');
        return Inertia::render('Run', [
            'run' => $run,
            'steps' => $run->steps,
            'task' => Task::findOrFail($run->task_id),
        ]);
    }

    public function showTask($id) {
        // Return the inspect-task view with just the task and its steps
        $task = Task::with('steps')->findOrFail($id);
        return view('inspect-task', [
            'task' => $task,
            'steps' => $task->steps,
        ]);
    }

    public function showStep($id) {
        // Return the inspect-step view with just the step and its input/output
        $step = Step::findOrFail($id);
        return Inertia('Step', [
            'step' => $step,
            'input' => $step->input,
            'output' => $step->output,
        ]);
    }
}
