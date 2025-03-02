<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
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
            $task = $developer->task()->where('task_id', $taskId)->first();

            $task->status = $validate['status'];
            $task->save();

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
}
