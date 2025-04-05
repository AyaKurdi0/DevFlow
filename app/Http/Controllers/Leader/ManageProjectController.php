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

            if (!$leader)
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!($leader->can('create project')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }

            $team = $leader->ownedTeam()->firstOrFail();

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


    ####################### Delete Existing Project By Leader #######################
    public function deleteProject($projectId) :JsonResponse
    {
        try {
            $leader = Auth::user();

            if (!$leader)
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!($leader->can('delete project')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }

            $project = projects::findOrFail($projectId);
            $tasks = $project->tasks()->get();

            foreach ($tasks as $task) {
                $task->delete();
            }
            $project->delete();
            return response()->json([
                'message' => 'Project deleted successfully',
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
            $leader = Auth::user();

            if (!$leader)
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!($leader->can('start project')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }
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
            $leader = Auth::user();
            if (!$leader)
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!($leader->can('complete project')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }
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


    ####################### Display All Projects To Leader #######################
    public function displayProjects(): JsonResponse
    {
        try {
            $leader = Auth::user();
            $team = $leader->ownedTeam()->firstOrFail();
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


    ####################### Display Project Information To Leader #######################
    public function displayProjectInfo($projectId): JsonResponse
    {
        try{
            $project = projects::findOrFail($projectId);
            return response()->json([
                'project' => $project,
            ],201);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }


    ####################### Display Newest Three Projects To Leader #######################
    public function displayNewestProjects(): JsonResponse
    {
        try {
            $leader = Auth::user();
            if (!$leader) {
                return response()->json([
                    'message' => 'No authenticated user found',
                    'success' => false
                ], 401);
            }
            $team = $leader->ownedTeam()->firstOrFail();
            $projects = Projects::Where('team_id', $team->id)
                ->latest()
                ->take(3)
                ->get();

            return response()->json([
                'projects' => $projects,
                'message' => 'Latest projects retrieved successfully',
                'success' => true
            ], 200);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

}
