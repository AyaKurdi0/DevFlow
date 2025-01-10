<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageTeamController extends Controller
{
    public function createTeam(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'Description' => 'nullable|string',
        ]);

        $leader = Auth::user();

        if (!$leader) {
            return response()->json([
                'message' => 'You are not authorized to access this page.',
            ]);
        }

        $team = Team::create([
            'team_name' => $data['name'],
            'Description' => $data['Description'],
            'user_id' => $leader->id,
        ]);

        return response()->json([
            'message' => 'Team created successfully',
            'team' => $team,
        ]);
    }

    public function updateTeam(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'Description' => 'nullable|string',

        ]);
        $leader = Auth::user();
        $team = Team::find($id);
        $team->update([
            'name' => $data['name'],
            'Description' => $data['Description'],
        ]);
        return response()->json([
            'message' => 'Team updated successfully',
            'team' => $team,
        ]);
    }

    public function deleteTeam($id)
    {
        $leader = Auth::user();
        $team = Team::find($id);
        $team->delete();
        return response()->json([
            'message' => 'Team deleted successfully',
        ]);
    }
}
