<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\projects;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;


class ManageProjectController extends Controller
{

    ####################### Create New Project By Leader #######################
    public function createNewProject(Request $request): JsonResponse

    {
        $validate = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'integer|min:1|max:5'
        ]);

        try {
            $leader = Auth::user();
            $team = $leader->team()->firstOrFail();

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
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }

    }

    public function deleteProject($id)
    {

    }


    ####################### Update Project Information By Leader #######################
    public function updateProject(Request $request, $id): JsonResponse
    {
        $validate = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'integer|min:1|max:5'
        ]);

        try {
            $project = Projects::findOrFail($id);
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
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }


    ####################### Start Implement Project By Leader #######################
    public function startImplementProject($id): JsonResponse
    {
        try {
            $project = projects::findOrFail($id);

            if($project->status !== 'initial')
            {
                return response()->json([
                    'error' => 'Project already in progress.'
                ], 400);
            }

            $project->status = 'in progress';
            $project->start_date = Carbon::now();
            $project->save();

            return response()->json([
                'message' => 'Project started successfully',
                'start_date' => $project->start_date,
            ], 200);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }


    ####################### End Implement Project By Leader #######################
    public function endImplementProject($id): JsonResponse
    {
        try {
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
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }


    ####################### Display All Projects For Leader #######################
    public function displayProjects(): JsonResponse
    {
        try {
            $leader = Auth::user();
            $team = $leader->team()->firstOrFail();
            $projects = Projects::Where('team_id', $team->id)->get();
            return response()->json([
                'projects' => $projects,
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

}
