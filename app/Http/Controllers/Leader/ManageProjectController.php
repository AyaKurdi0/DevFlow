<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\projects;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageProjectController extends Controller
{

    ####################### Create New Project By Leader #######################
    public function createNewProject(Request $request): JsonResponse

    {
        $leader = Auth::user();
        $team = $leader->team()->firstOrFail();

        if(!$team)
        {
            return response()->json([
                'error' => 'Leader does not have a team.'
            ], 403);
        }

        $validate = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'string',
            'priority' => 'integer|min:1|max:5'
        ]);

        $project = (new projects)->create([
            'title' => $validate['title'],
            'description' => $validate['description'],
            'team_id' => $team->id,
            'priority' => $validate['priority'],
        ]);

        return response()->json([
            'message' => 'Project created successfully',
            'project' => $project,
        ]);

    }

    public function deleteProject($id)
    {

    }

}
