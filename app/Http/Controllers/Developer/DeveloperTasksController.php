<?php

namespace App\Http\Controllers\Developer;

use App\Events\GeneralNotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\review;
use App\Notifications\NewTaskReview;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeveloperTasksController extends Controller
{

    ####################### Display Related Tasks To Developer #######################
    public function displayTasks(): JsonResponse
    {
        try {
            $developer = Auth::user();
            $developer_task = $developer->task()->get();

            return response()->json($developer_task);
        }
        catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage()
            ]);
        }
    }


    ####################### Display Selected Task Info To Developer #######################
    public function displayTaskInfo($taskID): JsonResponse
    {
        $developer = Auth::user();
        $task = $developer->task()->where('task_id', $taskID)->first();

        return response()->json($task);
    }


    ####################### Update Selected Task Status By Developer #######################
    public function updateTaskStatus(Request $request, $taskId): JsonResponse
    {
        $validate = $request->validate([
            'status' => 'required|string|in:To Do,Doing,Done',
        ]);
        try {
            $developer = Auth::user();
            if (!$developer) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'success' => false,
                ]);
            }
            if (!($developer->can('update task status')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }
            $team = $developer->teams()->get()->first();
            $teamLeader = $team->user()->get()->first();

            $task = $developer->task()->where('task_id', $taskId)->first();

            if (!$task) {
                return response()->json([
                    'message' => 'Task not found',
                ]);
            }

            $task->status = $validate['status'];
            $task->save();

            if($validate['status'] == 'Done')
            {
                $reviewTask = (new review)->create([
                    'review_title' => $task->title. 'Review',
                    'task_id' => $taskId,
                    'leader_id' => $teamLeader->id,
                    'developer_id' => $developer->id,
                ]);

                $teamLeader->notify(new NewTaskReview($reviewTask,$task));
                broadcast(new GeneralNotificationEvent([
                    'type' => 'new_review',
                    'message' => 'The task "' . $task->title . '" has been completed by ' . $developer->name,
                    'developer_id' => $developer->id,
                ]));
            }

            return response()->json([
                "message" => "Task status updated successfully",
                'task' => $task
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage()
            ]);
        }

    }


    ####################### Display Newest Three Tasks To Developer #######################
    public function displayNewestTask(): JsonResponse
    {
        try {
            $developer = Auth::user();
            if (!$developer)
            {
                return response()->json([
                    "message" => "You're not logged in"
                ]);
            }
            $developer_task = $developer->task()
                ->latest()
                ->take(3)
                ->get();

            return response()->json($developer_task);
        }
        catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage()
            ]);
        }
    }
}
