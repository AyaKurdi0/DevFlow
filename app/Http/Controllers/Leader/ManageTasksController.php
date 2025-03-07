<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\review;
use App\Models\tasks;
use App\Models\User;
use App\Models\user_task;
use App\Notifications\NewTaskAssigned;
use Auth;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        $leader = Auth::user();

        $task = (new tasks)->create([
            'title' => $validate['title'],
            'type' => $validate['type'],
            'description' => $validate['description'],
            'due_date' => $validate['due_date'],
            'project_id' => $projectId,
            'priority' => $validate['priority'],
        ]);

        $taskReview = (new review)->create([
            'task_id' => $task->id,
            'leader_id' => $leader->id,
        ]);

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task,
            'review' => $taskReview
        ],201);
    }

//    public function updateTask(Request $request, $taskId) :JsonResponse
//    {
//
//    }

    ####################### Delete Task By Leader #######################
    public function deleteTask($taskId) :JsonResponse
    {
        try {
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
            $developer = User::findOrFail($validate['developer_id']);

            user_task::create([
                'task_id' => $taskId,
                'developer_id' => $developer->id,
            ]);

            $task = tasks::findOrFail($taskId);
            $developer->notify(new NewTaskAssigned($task));

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
            $developer = User::findOrFail($validate['developer_id']);
            user_task::where('task_id', $taskId)->where('developer_id', $developer->id)->delete();
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
