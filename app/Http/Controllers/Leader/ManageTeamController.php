<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageTeamController extends Controller
{
    ####################### Create Team By Leader #######################
    public function createTeam(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string',
        ]);

        try {
            $leader = Auth::user();

            if (!$leader) {
                return response()->json([
                    'message' => 'You are not authorized to access this page.',
                ]);
            }

            if (!($leader->can('create team'))) {
                return response()->json([
                    'message' => 'Forbidden access.',
                ]);
            }

            $team = Team::create([
                'team_name' => $data['name'],
                'user_id' => $leader->id,
            ]);

            return response()->json([
                'message' => 'Team created successfully',
                'team' => $team,
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => 'Unable to create team',
                'error' => $e->getMessage(),
            ]);
        }
    }


    ####################### Delete Team By Leader #######################
    public function deleteTeam(): JsonResponse
    {

        try {
            $leader = Auth::user();
            if (!$leader) {
                return response()->json([
                    'message' => 'You are not authorized to access this page.',
                ]);
            }
            if (!($leader->can('delete team'))) {
                return response()->json([
                    'message' => 'Forbidden access',
                ]);
            }
            $team = $leader->ownedTeam()->first();
            if (!$team) {
                return response()->json([
                    'message' => 'You are not authorized to access this page.',
                ]);
            }
            $team->delete();
            return response()->json([
                'message' => 'Team deleted successfully',
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => 'Unable to delete team',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
