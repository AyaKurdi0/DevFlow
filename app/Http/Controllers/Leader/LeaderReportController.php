<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaderReportController extends Controller
{
    ####################### Display All Reports For Leader #######################
    public function getAllReports() : JsonResponse
    {
        try {
            $leader = Auth::user();

            $reports = $leader->ownedTeam()
                ->with(['projects.tasks.report' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                ->get()
                ->flatMap(function ($team) {
                    return $team->projects->flatMap(function ($project) {
                        return $project->tasks->flatMap(function ($task) {
                            return $task->report->map(function ($report) use ($task) {
                                return [
                                    'task_id' => $task->id,
                                    'task_title' => $task->title,
                                    'report_id' => $report->id,
                                    'report_title' => $report->title,
                                    'developer_id' => $report->developer_id,
                                    'created_at' => $report->created_at,
                                ];
                            });
                        });
                    });
                });

            return response()->json($reports);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }


    ####################### Display Selected Report Information For Leader #######################
    public function getReportInfo($reportId) : JsonResponse
    {
        try {
            $report = report::findOrFail($reportId);
            if (!$report) {
                return response()->json([
                    'message' => 'Report not found',
                ]);
            }
            return response()->json($report);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
