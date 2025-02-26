<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\tasks;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManageTasksController extends Controller
{
    public function creatNewTask(Request $request, $projectId) :JsonResponse
    {
        $validate = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|max:255',
            'due_date' => 'required|date',
            'priority' => 'required|integer|between:1,5',
        ]);

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
            'task' => $task
        ],201);
    }

//    public function updateTask(Request $request, $taskId) :JsonResponse
//    {
//
//    }

    public function deleteTask($taskId) :JsonResponse
    {
        try {
            $task = tasks::findOrFail($taskId);
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
}
