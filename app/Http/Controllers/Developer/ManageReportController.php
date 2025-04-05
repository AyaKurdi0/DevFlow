<?php

namespace App\Http\Controllers\Developer;

use App\Events\GeneralNotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\report;
use App\Models\tasks;
use App\Notifications\NewReportAdded;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageReportController extends Controller
{
    ####################### Add Report To Task By Developer #######################
    public function addReport(Request $request, $taskId) :JsonResponse
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            'challenges' => 'nullable|string',
            'result' => 'nullable|string',
        ]);

        try {
            $developer = Auth::user();
            if (!$developer) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'success' => false,
                ]);
            }
            if (!($developer->can('add report')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }

            $report = report::create([
                'task_id' => $taskId,
                'title' => $validatedData['title'],
                'details' => $validatedData['details'],
                'challenges' => $validatedData['challenges'],
                'result' => $validatedData['result'],
                'developer_id' => $developer->id,
            ]);

            $task = tasks::find($taskId);
            $team = $developer->teams;
            $leaders = $team->user;

            $leaders->notify(new NewReportAdded($report, $task, $developer));
            broadcast(new GeneralNotificationEvent([
                'type' => 'new_report_added',
                'report_id' => $this->report->id,
                'message' => "New report added for task {$task->title} by {$developer->name}",
            ]));

            return response()->json([
                'report' => $report,
                'message' => 'Report added successfully',
                'success' => true,
            ]);

        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }


    ####################### Display Developer's Reports #######################
    public function displayDeveloperReport() : JsonResponse
    {
        try {
            $developer = Auth::user();
            if (!$developer) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'success' => false,
                ], 401);
            }

            $reports = report::where('developer_id',$developer->id)->orderByDesc('created_at')->get();
            return response()->json([
                'reports' => $reports,
                'success' => true,
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ]);
        }

    }
}
