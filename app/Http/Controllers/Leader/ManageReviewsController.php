<?php

namespace App\Http\Controllers\Leader;

use App\Events\GeneralNotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\review;

//use App\Models\tasks;
use App\Models\tasks;
use App\Models\User;
use App\Notifications\TaskApproved;
use App\Notifications\TaskRejected;
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
            $leader = Auth::user();
            if (!$leader)
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!($leader->can('approve task')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }

            $review = review::findOrFail($reviewId);
            $review->reviewStatus = 'approved';
            $review->save();
            $task_id = $review->task_id;
            $task = tasks::findOrFail($task_id);
            $developer_id = $review->developer_id;
            $developer = User::findOrFail($developer_id);

            $developer->notify(new TaskApproved($task));
            broadcast(new GeneralNotificationEvent([
                'type' => 'task_approved',
                'task_id' => $task_id,
                'developer_id' => $developer_id,
                'massage' => "The task {$task->title} has been approved by leader.",
            ]));

            return response()->json([
                'message' => 'Task approved successfully',
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
    }


    ####################### Reject Task By Leader #######################
    public function rejectTask($reviewId): JsonResponse
    {
        try {
            $leader = Auth::user();
            if (!$leader)
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!($leader->can('reject task')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }

            $review = review::findOrFail($reviewId);
            $review->reviewStatus = 'rejected';
            $review->save();
            $task_id = $review->task_id;
            $task = tasks::findOrFail($task_id);
            $developer_id = $review->developer_id;
            $developer = User::findOrFail($developer_id);

            $developer->notify(new TaskRejected($task));
            broadcast(new GeneralNotificationEvent([
                'type' => 'task_rejected',
                'task_id' => $task_id,
                'developer_id' => $developer_id,
                'massage' => "The task {$task->title} has been rejected by leader.",
            ]));

            return response()->json([
                'message' => 'Task rejected successfully',
            ], 201);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
    }


    ####################### Reject Task By Leader #######################
    public function addComment(Request $request, $reviewId): JsonResponse
    {
        $validate = $request->validate([
            'comment' => 'required|string',
        ]);
        try {
            $leader = Auth::user();
            if (!$leader)
            {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!($leader->can('add comment on task review')))
            {
                return response()->json([
                    'massage' => 'Forbidden access.'
                ]);
            }
            $review = review::findOrFail($reviewId);

            $review->comment = $validate['comment'];
            $review->save();

            return response()->json([
                'message' => 'Comment added successfully',
            ], 201);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
    }


    ####################### Display All Review_Tasks To Leader #######################
    public function displayAllCompletedTasksToReview(): JsonResponse
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
        } catch (Exception $exception) {
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

            if (!$reviews->isEmpty()) {
                foreach ($reviews as $review) {
                    if ($review->reviewStatus === 'pending') {
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
        } catch (Exception $exception) {
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

            if (!$reviews->isEmpty()) {
                foreach ($reviews as $review) {
                    if ($review->reviewStatus === 'approved') {
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
        } catch (Exception $exception) {
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

            if (!$reviews->isEmpty()) {
                foreach ($reviews as $review) {
                    if ($review->reviewStatus === 'rejected') {
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
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
    }


    ####################### Display Review Details To Leader #######################
    public function displayReviewInfo($reviewId): JsonResponse
    {
        try {
            $review = review::findOrFail($reviewId);

            if (!$review) {
                return response()->json([
                    'message' => 'Review not found',
                ]);
            }
            $task = $review->task()->firstOrFail();
            return response()->json([
                'task' => $task,
                'review' => $review,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
