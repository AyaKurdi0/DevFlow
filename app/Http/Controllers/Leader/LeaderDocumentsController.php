<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\documents;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeaderDocumentsController extends Controller
{
    ####################### Download File For Task By Any Members #######################
    public function downloadFile($documentId): StreamedResponse|JsonResponse
    {
        try {
            $document = documents::findOrFail($documentId);
            return Storage::download('public/' . $document->path, $document->title);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
