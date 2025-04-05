<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\documents;
use App\Models\tasks;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeaderDocumentsController extends Controller
{
    ####################### Download File For Task By Any Members #######################
    public function downloadFile($documentId): StreamedResponse|JsonResponse
    {
        try {
            $leader = Auth::user();
            if (!$leader)
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!($leader->can('download task files')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }
            $document = documents::findOrFail($documentId);
            return Storage::download('public/' . $document->path, $document->title);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }


    ####################### Displaying All File's Task To Leader #######################
    public function getAllTaskDocuments($taskId): JsonResponse
    {
        try {
            $task = tasks::findOrFail($taskId);
            $documents = $task->document()->get();

            if ($documents->isEmpty())
            {
                return response()->json([
                    'massage' => 'No documents found.'
                ]);
            }

            return response()->json($documents);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
