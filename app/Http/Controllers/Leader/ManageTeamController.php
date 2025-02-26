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

//    public function updateTeam(Request $request, $id)
//    {
//        $data = $request->validate([
//            'name' => 'required|string',
//            'Description' => 'nullable|string',
//
//        ]);
//        try {
//            $leader = Auth::user();
//            $team = Team::find($id);
//            $team->update([
//                'name' => $data['name'],
//                'Description' => $data['Description'],
//            ]);
//            return response()->json([
//                'message' => 'Team updated successfully',
//                'team' => $team,
//            ]);
//        }
//        catch (\Exception $e) {
//            return response()->json([
//                'message' => 'Unable to update team',
//                'error' => $e->getMessage(),
//            ]);
//        }
//    }


    ####################### Delete Team By Leader #######################
    public function deleteTeam($id)
    {
        try {
            $leader = Auth::user();
            $team = Team::find($id);
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
