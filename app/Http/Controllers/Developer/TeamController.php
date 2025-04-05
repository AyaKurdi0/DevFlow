<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{

    ####################### Display Team Member To Developer #######################
    public function displayTeamMemberToDeveloper() : JsonResponse
    {
        try {
            $developer = Auth::user();
            $team = $developer->teams()->firstOrFail();
            $teamMembers = $team->members()->get();
            $teamLeader = $team->user()->firstOrFail();

            return response()->json([
                'leader' => $teamLeader,
                'members' => $teamMembers,
            ]);
        }
        catch (Exception $exception)
        {
            return response()->json([
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
