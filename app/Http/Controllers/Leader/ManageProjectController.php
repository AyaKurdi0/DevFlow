<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\projects;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ManageProjectController extends Controller
{

    ####################### Create New Project By Leader #######################
    public function createNewProject(Request $request): JsonResponse

    {
        $leader = Auth::user();
        $team = $leader->team()->firstOrFail();

        $validate = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'integer|min:1|max:5'
        ]);

        $project = Projects::create([
            'title' => $validate['title'],
            'description' => $validate['description'] ?? '',
            'team_id' => $team->id,
            'due_date' => $validate['due_date'] ?? null,
            'priority' => $validate['priority'],
        ]);

        return response()->json([
            'message' => 'Project created successfully',
            'project' => $project,
        ], 201);

    }

    public function deleteProject($id)
    {

    }


    ####################### Update Project Information By Leader #######################
    public function updateProject(Request $request, $id): JsonResponse
    {
        $project = Projects::findOrFail($id);
        $validate = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'integer|min:1|max:5'
        ]);

        $project->update([
            'title' => $validate['title'],
            'description' => $validate['description'] ?? '',
            'due_date' => $validate['due_date'] ?? null,
            'priority' => $validate['priority'],
        ]);

        return response()->json([
            'message' => 'Project updated successfully',
            'project' => $project,
        ]);
    }


    ####################### Start Implement Project By Leader #######################
    public function startImplementProject($id): JsonResponse
    {
        $project = projects::findOrFail($id);

        if($project->status !== 'initial')
        {
            return response()->json([
                'error' => 'Project already in progress.'
            ], 400);
        }

        $project->status = 'in progress';
        $project->start_date = Carbon::now();;
        $project->save();

        return response()->json([
            'message' => 'Project started successfully',
            'start_date' => $project->start_date,
        ], 200);
    }


    ####################### End Implement Project By Leader #######################
    public function endImplementProject($id): JsonResponse
    {
        $project = projects::findOrFail($id);

        if($project->status === 'completed')
        {
            return response()->json([
                'error' => 'Project already completed.'
            ], 400);
        }

        $project->status = 'completed';
        $project->end_date = Carbon::now();
        $project->save();

        return response()->json([
            'message' => 'Project completed successfully',
            'start_date' => $project->start_date,
        ], 200);
    }

}
