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

    $name = request('name');// create agent in database
$agent = Agent::create([
    'user_id' => auth()->user()->id,
    'name' => $name,
]);

return response()->json([
    'agent_id' => $agent->id, // add the newly created agent's id to the response
    'name' => $name,
], 201);
}
}
