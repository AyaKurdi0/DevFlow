<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class DeveloperReviewsController extends Controller
{

    ####################### Display All Reviews For Developer #######################
    public function displayAllDeveloperReview(): JsonResponse
    {
        try {
            $developer = Auth::user();
            $tasks = $developer->task()->get();

            foreach ($tasks as $task) {
                $reviews = $task->reviews()->get();
                foreach ($reviews as $review) {
                    return response()->json([
                        'task id' => $task->id,
                        'task title' => $task->title,
                        'reviews' => $review,
                    ]);
                }
            }
            return response()->json([
                'message' => 'no reviews found',
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }


    ####################### Display Approved Reviews For Developer #######################
    public function displayApprovedDeveloperReviews(): JsonResponse
    {
        try {
            $developer = Auth::user();
            $tasks = $developer->task()->get();

            foreach ($tasks as $task) {
                $reviews = $task->reviews()->get();
                foreach ($reviews as $review) {
                    if($review->status == 'approved'){
                        return response()->json([
                            'task id' => $task->id,
                            'task title' => $task->title,
                            'reviews' => $review,
                        ]);
                    }
                }
            }
            return response()->json([
                'message' => 'no reviews found',
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }


    ####################### Display Rejected Reviews For Developer #######################
    public function displayRejectedDeveloperReviews(): JsonResponse
    {
        try {
            $developer = Auth::user();
            $tasks = $developer->task()->get();

            foreach ($tasks as $task) {
                $reviews = $task->reviews()->get();
                foreach ($reviews as $review) {
                    if($review->status == 'rejected'){
                        return response()->json([
                            'task id' => $task->id,
                            'task title' => $task->title,
                            'reviews' => $review,
                        ]);
                    }
                }
            }
            return response()->json([
                'message' => 'no reviews found',
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

}
