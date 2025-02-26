<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskDisplayingController extends Controller
{
    public function displayTask(): JsonResponse
    {
        $developer = Auth::user();
        $developer_task = $developer->task()->get();

        return response()->json($developer_task);
    }

    public function displayTaskInfo($taskID): JsonResponse
    {
        $developer = Auth::user();
        $task = $developer->task()->where('id', $taskID)->first();

        return response()->json($task);
    }
}
