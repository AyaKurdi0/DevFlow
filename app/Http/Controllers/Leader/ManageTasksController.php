<?php

namespace App\Http\Controllers\Leader;

use App\Events\GeneralNotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\review;
use App\Models\tasks;
use App\Models\User;
use App\Models\user_task;
use App\Notifications\NewTaskAssigned;
use App\Notifications\UnassignTask;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageTasksController extends Controller
{

    ####################### Create New Task By Leader #######################
    public function creatNewTask(Request $request, $projectId) :JsonResponse
    {

        $validate = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|max:255',
            'due_date' => 'required|date',
            'priority' => 'required|integer|between:1,5',
        ]);

        try {
            $leader = Auth::user();

            if (!$leader)
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!($leader->can('create task')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }

            $task = (new tasks)->create([
                'title' => $validate['title'],
                'type' => $validate['type'],
                'description' => $validate['description'],
                'due_date' => $validate['due_date'],
                'project_id' => $projectId,
                'priority' => $validate['priority'],
            ]);

            return response()->json([
                'message' => 'Task created successfully',
                'task' => $task,
            ],201);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }


    ####################### Delete Task By Leader #######################
    public function deleteTask($taskId) :JsonResponse
    {
        try {
            $leader = Auth::user();

            if (!$leader)
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!($leader->can('delete task')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }

            $task = tasks::findOrFail($taskId);
            $userTasks = user_task::where('task_id', $taskId)->get();
            foreach ($userTasks as $userTask) {
                $userTask->delete();
            }

            $taskReviews = review::where('task_id', $taskId)->get();
            foreach ($taskReviews as $taskReview) {
                $taskReview->delete();
            }

            $task->delete();


            return response()->json([
                'message' => 'Task deleted successfully'
            ]);
        }
        catch (Exception $exception)
        {
            return response()->json([
                'message' => $exception->getMessage()
            ]);
        }
    }


    ####################### Assign Task To Developer By Leader #######################
    public function assignTask(Request $request, $taskId) :JsonResponse
    {
        $validate = $request->validate([
            'developer_id' => 'required|integer|exists:users,id',
        ]);
        try {
            $leader = Auth::user();

            if (!$leader)
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!($leader->can('assign task')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }

            $developer = User::findOrFail($validate['developer_id']);
            user_task::create([
                'task_id' => $taskId,
                'developer_id' => $developer->id,
            ]);

            $task = tasks::findOrFail($taskId);

            $developer->notify(new NewTaskAssigned($task));
            broadcast(new GeneralNotificationEvent([
                'type' => 'task_assigned',
                'message' => "Task '{$task->title}' has been assigned to {$developer->name}.",
                'task_id' => $task->id,
                'developer_id' => $developer->id,
            ]));

            return response()->json([
                'message' => 'Task assigned successfully'
            ], 201);
        }
        catch (Exception $exception)
        {
            return response()->json([
                'message' => $exception->getMessage()
            ]);
        }
    }


    ####################### Unassign Task from Developer By Leader #######################
    public function unassignTask(Request $request, $taskId) :JsonResponse
    {
        $validate = $request->validate([
            'developer_id' => 'required|integer|exists:users,id',
        ]);
        try {
            $leader = Auth::user();

            if (!$leader)
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!($leader->can('unassign task')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }

            $developer = User::findOrFail($validate['developer_id']);
            $task = tasks::findOrFail($taskId);

            user_task::where('task_id', $taskId)->where('developer_id', $developer->id)->delete();

            $developer->notify(new UnassignTask($task));
            broadcast(new GeneralNotificationEvent([
                'type' => 'task_unassigned',
                'message' => "Task '{$task->title}' has been unassigned",
                'task_id' => $task->id,
                'developer_id' => $developer->id,
            ]));

            return response()->json([
                'message' => 'Task unassigned successfully'
            ], 201);
        }
        catch (Exception $exception)
        {
            return response()->json([
                'message' => $exception->getMessage()
            ]);
        }

    }


    ####################### Display All Task To Leader #######################
    public function displayAllTasks(): JsonResponse
    {
        try {
            $leader = Auth::user();

            if (!$leader) {
                return response()->json(['message' => 'Leader not found'], 404);
            }

            $tasks = $leader->ownedTeam()
                ->with('projects.tasks.project')
                ->get()
                ->flatMap(function ($team) {
                    return $team->projects->flatMap(function ($project) {
                        return $project->tasks->map(function ($task) use ($project) {
                            return [
                                'task_id' => $task->id,
                                'task_title' => $task->title,
                                'task_type' => $task->type,
                                'status' => $task->status,
                                'priority' => $task->priority,
                                'due_date' => $task->due_date,
                                'project_id' => $project->id,
                                'project_title' => $project->title,
                            ];
                        });
                    });
                });

            return response()->json([
                'message' => 'All tasks retrieved successfully',
                'tasks' => $tasks,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }


    ####################### Display Task Info To Leader #######################
    public function displayTaskInfo($taskId) :JsonResponse
    {
        try {
            $task = tasks::findOrFail($taskId);
            return response()->json([
                'task' => $task
            ], 201);
        }
        catch (Exception $exception)
        {
            return response()->json([
                'message' => $exception->getMessage()
            ]);
        }
    }

}
