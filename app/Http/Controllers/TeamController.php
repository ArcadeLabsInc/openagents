<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function threads(Team $team)
    {
        $threads = $team->threads()->with('project')->get();
        return response()->json($threads, 200);
    }

    public function getTeamsAndProjects()
    {
        $user = Auth::user();
        $teams = $user->teams()->pluck('name', 'id')->toArray();
        
        // Get the active team or null for personal context
        $activeTeam = $user->currentTeam;
        
        // Get projects for the active team or personal projects
        if ($activeTeam) {
            $projects = Project::where('team_id', $activeTeam->id)->pluck('name', 'id')->toArray();
        } else {
            $projects = Project::where('team_id', null)->where('user_id', $user->id)->pluck('name', 'id')->toArray();
        }

        return view('components.sidebar.team-switcher-content', compact('teams', 'projects', 'activeTeam'));
    }

    public function switchTeam(Request $request, Team $team)
    {
        $user = Auth::user();
        
        // Check if the user belongs to the team
        if (!$user->teams->contains($team)) {
            return redirect()->back()->with('error', 'You do not have access to this team.');
        }

        $user->current_team_id = $team->id;
        $user->save();

        return $this->getTeamsAndProjects();
    }
}