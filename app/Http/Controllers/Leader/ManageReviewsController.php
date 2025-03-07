<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\review;
//use App\Models\tasks;
use App\Models\tasks;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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


    ####################### Display All Review_Tasks To Leader #######################
    public function displayAllTasksToReview(): JsonResponse
    {
        try {
            $leader = Auth::user();
            $reviews = review:: where('leader_id', $leader->id)->get();

            if (!$reviews->isEmpty()) {
                foreach ($reviews as $review) {
                    $task = tasks::findOrFail($review->task_id);
                    return response()->json([
                        'task' => $task,
                        'review' => $review,
                    ]);
                }
            }
            return response()->json([
                'message' => 'No reviews available',
            ]);
        }
        catch (Exception $exception)
        {
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
    }


    ####################### Display All Tasks That's Need To Review To Leader #######################
    public function displayUnreviewedTasks(): JsonResponse
    {
        try {
            $leader = Auth::user();
            $reviews = review::where('leader_id', $leader->id)->get();

            if(!$reviews->isEmpty())
            {
                foreach ($reviews as $review)
                {
                    if($review->status === 'pending')
                    {
                        $task = tasks::findOrFail($review->task_id);
                        return response()->json([
                            'task' => $task,
                            'reviews' => $review,
                        ]);
                    }
                }
            }
            return response()->json([
                'message' => 'No reviews available',
            ]);
        }
        catch (Exception $exception)
        {
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
    }


    ####################### Display All Approved Tasks To Leader #######################
    public function displayApprovedTasks(): JsonResponse
    {
        try {
            $leader = Auth::user();
            $reviews = review::where('leader_id', $leader->id)->get();

            if(!$reviews->isEmpty())
            {
                foreach ($reviews as $review)
                {
                    if($review->status === 'approved')
                    {
                        $task = tasks::findOrFail($review->task_id);
                        return response()->json([
                            'task' => $task,
                            'reviews' => $review,
                        ]);
                    }
                }
            }
            return response()->json([
                'message' => 'No reviews available',
            ]);
        }
        catch (Exception $exception)
        {
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
    }


    ####################### Display All Rejected Tasks To Leader #######################
    public function displayRejectedTasks(): JsonResponse
    {
        try {
            $leader = Auth::user();
            $reviews = review::where('leader_id', $leader->id)->get();

            if(!$reviews->isEmpty())
            {
                foreach ($reviews as $review)
                {
                    if($review->status === 'rejected')
                    {
                        $task = tasks::findOrFail($review->task_id);
                        return response()->json([
                            'task' => $task,
                            'reviews' => $review,
                        ]);
                    }
                }
            }
            return response()->json([
                'message' => 'No reviews available',
            ]);
        }
        catch (Exception $exception)
        {
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
    }



}
