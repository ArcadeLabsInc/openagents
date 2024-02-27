<?php

namespace App\Http\Controllers;

class ConversationController extends Controller
{
    public function store()
    {
        request()->validate([
            'agent_id' => 'required',
        ]);

        // Given we have an authenticated user
        request()->user()->conversations()->create([
            'agent_id' => request('agent_id'),
        ]);

        return response()->json([], 201);
    }
}
