<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\documents;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageDocumentsController extends Controller
{
    ####################### Upload File For Task By Developer #######################
    public function uploadFiles(Request $request, $taskId): JsonResponse
    {
        $validate = $request->validate([
            'file' => 'required|mimes:pdf,doc,docx,png,jpg,jpeg,svg|max:2048'
        ]);

        try {
            $developer = Auth::user();

            if (!$developer) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'success' => false,
                ]);
            }
            if (!($developer->can('upload task files')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = $file->getClientOriginalName();
                $filePath = $file->storeAs('documents', $fileName, 'public');

                $document = documents::create([
                    'title' => $fileName,
                    'path' => $filePath,
                    'task_id' => $taskId,
                    'document_type' => $file->getClientOriginalExtension(),
                    'uploaded_by' => $developer->id,
                    'uploaded_date' => now(),
                ]);

                return response()->json([
                    'message' => 'File uploaded successfully',
                    'document' => $document,
                ], 201);
            }

            return response()->json([
                'message' => 'No file uploaded',
            ], 400);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
