<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\review;
//use App\Models\tasks;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManageReviewsController extends Controller
{

    ####################### Approved Task By Leader #######################
    public function approveTask($reviewId): JsonResponse
    {
        try {
            $review = review::findOrFail($reviewId);
            $review->status = 'approved';
            $review->save();

            return response()->json([
                'message' => 'Task approved successfully',
            ]);
        }
        catch (Exception $exception)
        {
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
    }


    ####################### Reject Task By Leader #######################
    public function rejectTask($reviewId): JsonResponse
    {
        try {
            $review = review::findOrFail($reviewId);
            $review->status  = 'rejected';
            $review->save();

            return response()->json([
                'message' => 'Task rejected successfully',
            ],201 );
        }
        catch (Exception $exception)
        {
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
    }


    ####################### Reject Task By Leader #######################
    public function addComment(Request $request, $reviewId): JsonResponse
    {
        $validate = $request->validate([
            'comment' => 'required',
        ]);
        try {
            $review = review::findOrFail($reviewId);

            $review->comment = $validate['comment'];
            $review->save();

            return response()->json([
                'message' => 'Comment added successfully',
            ], 201);
        }
        catch (Exception $exception)
        {
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
