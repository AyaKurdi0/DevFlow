<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManageTeamController extends Controller
{
    ####################### Create Team By Leader #######################
    public function createTeam(Request $request): JsonResponse
    {
        $data = $request->validate([
            "manager_name" => "required|string",
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
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Unable to create team',
                'error' => $e->getMessage(),
            ]);
        }
    }


    ####################### Delete Team By Leader #######################
    public function deleteWorkspace(): JsonResponse
    {
        try {
            $leader = Auth::user();

            if (!$leader) {
                return response()->json([
                    'message' => 'You are not authorized to access this page.',
                ], 403);
            }

            if (!$leader->can('delete team')) {
                return response()->json([
                    'message' => 'Forbidden access',
                ], 403);
            }

            $team = $leader->ownedTeam()->first();

            if (!$team) {
                return response()->json([
                    'message' => 'You do not own any team.',
                ], 404);
            }

            $teamId = $team->id;

            DB::transaction(function () use ($teamId) {
                $team = Team::with([
                    'messages',
                    'projects.tasks.report',
                    'projects.tasks.reviews',
                    'projects.tasks.document',
                    'projects.tasks.developers',
                    'projects.tasks',
                    'projects',
                ])->findOrFail($teamId);

                // Delete messages
                $team->messages()->delete();

                // Delete projects and their related data
                foreach ($team->projects as $project) {
                    foreach ($project->tasks as $task) {
                        $task->report()->delete();
                        $task->reviews()->delete();
                        $task->document()->delete();
                        $task->developers()->detach(); // Detach developers from tasks
                    }
                    $project->tasks()->delete();
                }
                $team->projects()->delete();

                // Detach members from the team (removes rows from team__members)
                $team->members()->detach();

                // Finally, delete the team
                $team->delete();
            });

            return response()->json([
                'message' => 'Team and all related data deleted successfully.',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Unable to delete team',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
