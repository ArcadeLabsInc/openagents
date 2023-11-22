<?php


namespace App\Http\Controllers;

use App\Models\Agent;
use Inertia\Inertia;
use Inertia\Response;

class AgentController extends Controller
{
  public function store() {
    request()->validate([
      'name' => 'required',
    ]);

    $name = request('name');
// create agent in database
$agent = Agent::create([
  'user_id' => auth()->user()->id,
  'name' => $name,
  'memory_manager' => null, // add a default value for the memory manager field
]);
return response()->json([
      'name' => $name,
    ], 201);
  }
}
