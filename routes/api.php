<?php

use App\Http\Controllers\Auth\GitHubServicesController;
use App\Http\Controllers\Auth\GoogleServicesController;
use App\Http\Controllers\Auth\ManageProfileController;
use App\Http\Controllers\Connection\ChatController;
use App\Http\Controllers\Developer\DeveloperReviewsController;
use App\Http\Controllers\Developer\DeveloperTasksController;
use App\Http\Controllers\Developer\ManageDocumentsController;
use App\Http\Controllers\Developer\ManageReportController;
use App\Http\Controllers\Developer\TeamController;
use App\Http\Controllers\Leader\LeaderDocumentsController;
use App\Http\Controllers\Leader\LeaderReportController;
use App\Http\Controllers\Leader\ManageProjectController;
use App\Http\Controllers\Leader\ManageReviewsController;
use App\Http\Controllers\Leader\ManageTasksController;
use App\Http\Controllers\Leader\ManageTeamController;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Leader\ManageMemberController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', fn(Request $request) => $request->user());
Route::get('/register', [RegistrationController::class, 'leaderRegister'])
    ->name('leader.register');
Route::get('/login', [RegistrationController::class, 'login'])
    ->name('login');
Route::middleware('auth:sanctum')->post('/logout', [RegistrationController::class, 'logout'])
    ->name('logout');

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::prefix('google')->group(function () {
    Route::get('/',[GoogleServicesController::class, 'redirect'])
        ->name('google.redirect');
    Route::get('/callback',[GoogleServicesController::class, 'callback'])
        ->name('google.callback');
});

// GitHub Authentication Routes
Route::prefix('auth/github')->group(function () {
    Route::get('/', [GitHubServicesController::class, 'redirect'])
        ->name('github.redirect');
    Route::get('/callback', [GitHubServicesController::class, 'callback'])
        ->name('github.callback')
        ->middleware('web');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/repository', [GitHubServicesController::class, 'getUserRepositories'])
            ->name('github.getUserRepositories');
    });
});

// Leader Routes
Route::middleware('auth:sanctum')->prefix('leader')->group(function () {
    // Team Management
    Route::prefix('manageTeam')->group(function () {
        Route::post('/createTeam', [ManageTeamController::class, 'createTeam'])
            ->name('leader.manageTeam.createTeam');
        Route::post('/deleteTeam', [ManageTeamController::class, 'deleteTeam'])
            ->name('leader.manageTeam.deleteTeam');
    });

    // Member Management
    Route::prefix('manageMember')->group(function () {
        Route::post('/addDeveloper', [ManageMemberController::class, 'addDeveloper'])
            ->name('leader.manageMember.addDeveloper');
        Route::post('/deleteDeveloper/{id}', [ManageMemberController::class, 'removeDeveloper'])
            ->name('leader.manageMember.removeDeveloper');
        Route::post('/assignPermission/{id}', [ManageMemberController::class, 'assignPermission'])
            ->name('leader.manageMember.assignPermission');
        Route::post('/unsignedPermission/{id}', [ManageMemberController::class, 'unsignedPermission'])
            ->name('leader.manageMember.unsignedPermission');
        Route::get('/displayMembers', [ManageMemberController::class, 'displayTeamMembersToLeader'])
            ->name('leader.manageMember.displayTeamMembers');
    });

    // Project Management
    Route::prefix('manageProject')->group(function () {
        Route::post('/createProject', [ManageProjectController::class, 'createNewProject'])
            ->name('leader.manageProject.createProject');
        Route::post('/deleteProject/{id}', [ManageProjectController::class, 'deleteProject'])
            ->name('leader.manageProject.deleteProject');
        Route::post('/startingProject/{id}', [ManageProjectController::class, 'startImplementProject'])
            ->name('leader.manageProject.startingProject');
        Route::post('/completeProject/{id}', [ManageProjectController::class, 'endImplementProject'])
            ->name('leader.manageProject.completeProject');
        Route::get('/displayProjects', [ManageProjectController::class, 'displayProjects'])
            ->name('leader.manageProject.displayProjects');
        Route::get('/displayNewestProjects', [ManageProjectController::class, 'displayNewestProjects'])
            ->name('leader.manageProject.displayNewestProjects');
        Route::get('/displayProjectInfo/{id}', [ManageProjectController::class, 'displayProjectInfo'])
            ->name('leader.manageProject.displayProjectInfo');
    });

    // Task Management
    Route::prefix('manageTask')->group(function () {
        Route::post('/createTask/{id}', [ManageTasksController::class, 'creatNewTask'])
            ->name('leader.manageTask.createTask');
        Route::post('/assignTask/{id}', [ManageTasksController::class, 'assignTask'])
            ->name('leader.manageTask.assignTask');
        Route::post('/deleteTask/{id}', [ManageTasksController::class, 'deleteTask'])
            ->name('leader.manageTask.deleteTask');
        Route::post('/unassignTask/{id}', [ManageTasksController::class, 'unassignTask'])
            ->name('leader.manageTask.unassignTask');
        Route::get('/displayTask/{id}', [ManageTasksController::class, 'displayTaskInfo'])
            ->name('leader.manageTask.displayTasks');
        Route::get('/displayAllTasks', [ManageTasksController::class, 'displayAllTasks'])
            ->name('leader.manageTask.displayAllTasks');
    });

    // Review Management
    Route::prefix('manageReview')->group(function () {
        Route::post('/approveTask/{id}', [ManageReviewsController::class, 'approveTask'])
            ->name('leader.manageReview.approveTask');
        Route::post('/rejectTask/{id}', [ManageReviewsController::class, 'rejectTask'])
            ->name('leader.manageReview.rejectTask');
        Route::post('/addComment/{id}', [ManageReviewsController::class, 'addComment'])
            ->name('leader.manageReview.addComment');
        Route::get('/displayAllCompletedTasksToReview', [ManageReviewsController::class, 'displayAllCompletedTasksToReview'])
            ->name('leader.manageReview.displayAllCompletedTasksToReview');
        Route::get('/displayUnreviewedTasks', [ManageReviewsController::class, 'displayUnreviewedTasks'])
            ->name('leader.manageReview.displayUnreviewedTasks');
        Route::get('/displayApprovedTasks', [ManageReviewsController::class, 'displayApprovedTasks'])
            ->name('leader.manageReview.displayApprovedTasks');
        Route::get('/displayRejectedTasks', [ManageReviewsController::class, 'displayRejectedTasks'])
            ->name('leader.manageReview.displayRejectedTasks');
        Route::get('/displayReviewInfo/{id}', [ManageReviewsController::class, 'displayReviewInfo'])
            ->name('leader.manageReview.displayReviewInfo');
    });

    // Leader Reports
    Route::prefix('leaderReport')->group(function () {
        Route::get('/getAllReports', [LeaderReportController::class, 'getAllReports'])
            ->name('leader.report.getAllReports');
        Route::get('/getReportInfo/{id}', [LeaderReportController::class, 'getReportInfo'])
            ->name('leader.report.getReportInfo');
    });

    // Leader Documents
    Route::prefix('leaderDocuments')->group(function () {
        Route::get('/downloadFile/{id}', [LeaderDocumentsController::class, 'downloadFile'])
            ->name('Developer.documentManage.downloadFile');
        Route::get('/getAllTaskDocuments/{id}', [LeaderDocumentsController::class, 'getAllTaskDocuments'])
            ->name('Developer.documentManage.getAllTaskDocuments');
    });
});

// Developer Routes
Route::middleware('auth:sanctum')->prefix('Developer')->group(function () {
    // Task Developer Displaying
    Route::prefix('taskDisplaying')->group(function () {
        Route::get('/getDeveloperTasks', [DeveloperTasksController::class, 'displayTasks'])
            ->name('Developer.taskDisplaying.getDeveloperTasks');
        Route::get('/getTaskInfo/{id}', [DeveloperTasksController::class, 'displayTaskInfo'])
            ->name('Developer.taskDisplaying.getTaskInfo');
        Route::post('updateTaskStatus/{id}', [DeveloperTasksController::class, 'updateTaskStatus'])
            ->name('Developer.taskDisplaying.updateTaskStatus');
        Route::get('/displayNewestTask', [DeveloperTasksController::class, 'displayNewestTask'])
            ->name('Developer.taskDisplaying.displayNewestTask');
    });

    // Documents Management
    Route::prefix('manageDocument')->group(function () {
        Route::post('/uploadFile/{id}', [ManageDocumentsController::class, 'uploadFiles'])
            ->name('Developer.documentManage.uploadFile');
    });

    // Developer Reviews
    Route::prefix('reviewDisplaying')->group(function () {
        Route::get('/getDeveloperReviews', [DeveloperReviewsController::class, 'displayAllDeveloperReview'])
            ->name('Developer.reviewDisplaying.getDeveloperReviews');
        Route::get('/getApprovedReviews', [DeveloperReviewsController::class, 'displayApprovedDeveloperReviews'])
            ->name('Developer.reviewDisplaying.getApprovedReviews');
        Route::get('/getRejectedReviews', [DeveloperReviewsController::class, 'displayRejectedDeveloperReviews'])
            ->name('Developer.reviewDisplaying.getRejectedReviews');
    });

    // Developer Team
    Route::prefix('developerTeam')->group(function () {
        Route::get('/displayTeamMember', [TeamController::class, 'displayTeamMemberToDeveloper'])
            ->name('DeveloperTeam.displayTeamMember');
    });

    // Reports Management
    Route::prefix('manageReports')->group(function () {
        Route::post('/addReport/{id}', [ManageReportController::class, 'addReport'])
            ->name('Developer.manageReports.addReport');
        Route::get('/displayDeveloperReport', [ManageReportController::class, 'displayDeveloperReport'])
            ->name('Developer.manageReports.displayDeveloperReport');
    });

});

// User Profile Routes
Route::middleware('auth:sanctum')->prefix('user/profile')->group(function () {
    Route::get('/displayInfo', [ManageProfileController::class, 'getUserInfo'])
        ->name('user.profile.displayInfo');
    Route::post('/updateInfo', [ManageProfileController::class, 'updateInfo'])
        ->name('user.profile.updateInfo');
    Route::post('/resetPassword', [ManageProfileController::class, 'resetPassword'])
        ->name('user.profile.resetPassword');
});

//Chat Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/sendMessage', [ChatController::class, 'sendMessage'])
        ->name('message.send');
    Route::get('/historyMessages', [ChatController::class, 'getAllMessages'])
        ->name('history.messages');
});

//    Route::post('t1', function () {
//        App\Models\Tasks::create([
//            'title' => 'Database Schema Optimization',
//            'type' => 'database',
//            'description' => 'Optimize database queries and indexes.',
//            'project_id' => 1,
//            'due_date' => '2024-01-15',
//            'start_date' => '2023-12-15',
//            'estimated_start_date' => '2023-12-15',
//            'estimated_end_date' => '2023-12-20',
//            'estimated_time_inDays' => 5,
//            'actual_time_inDays' => 6,
//            'status' => 'To Do',
//            'priority' => 3
//        ]);
//
//        App\Models\Tasks::create([
//            'title' => 'Frontend UI Design',
//            'type' => 'front-end',
//            'description' => 'Create responsive web interface designs.',
//            'project_id' => 1,
//            'due_date' => '2023-12-25',
//            'start_date' => '2023-12-10',
//            'estimated_start_date' => '2023-12-10',
//            'estimated_end_date' => '2023-12-20',
//            'estimated_time_inDays' => 10,
//            'actual_time_inDays' => 12,
//            'priority' => 5
//        ]);
//
//        App\Models\Tasks::create([
//            'title' => 'API Documentation',
//            'type' => 'back-end',
//            'description' => 'Document REST API endpoints and usage.',
//            'project_id' => 1,
//            'due_date' => '2024-01-05',
//            'start_date' => '2023-12-20',
//            'estimated_start_date' => '2023-12-20',
//            'estimated_end_date' => '2023-12-23',
//            'estimated_time_inDays' => 3,
//            'actual_time_inDays' => 4,
//            'priority' => 1
//        ]);
//
//        App\Models\Tasks::create([
//            'title' => 'Performance Testing',
//            'type' => 'back-end',
//            'description' => 'Conduct load and stress testing.',
//            'project_id' => 1,
//            'due_date' => '2024-01-20',
//            'start_date' => '2024-01-05',
//            'estimated_start_date' => '2024-01-05',
//            'estimated_end_date' => '2024-01-12',
//            'estimated_time_inDays' => 7,
//            'actual_time_inDays' => 8,
//            'priority' => 5
//        ]);
//
//        App\Models\Tasks::create([
//            'title' => 'Bug Fixing Phase 1',
//            'type' => 'back-end',
//            'description' => 'Resolve critical bugs reported in v1.0.',
//            'project_id' => 1,
//            'due_date' => '2024-01-10',
//            'start_date' => '2023-12-25',
//            'estimated_start_date' => '2023-12-25',
//            'estimated_end_date' => '2023-12-30',
//            'estimated_time_inDays' => 5,
//            'actual_time_inDays' => 6,
//            'priority' => 3
//        ]);
//
//        App\Models\Tasks::create([
//            'title' => 'Mobile App Integration',
//            'type' => 'front-end',
//            'description' => 'Integrate mobile app with backend services.',
//            'project_id' => 1,
//            'due_date' => '2024-02-05',
//            'start_date' => '2024-01-15',
//            'estimated_start_date' => '2024-01-15',
//            'estimated_end_date' => '2024-01-29',
//            'estimated_time_inDays' => 14,
//            'actual_time_inDays' => 15,
//            'priority' => 5
//        ]);
//
//        App\Models\Tasks::create([
//            'title' => 'User Manual Drafting',
//            'type' => 'front-end',
//            'description' => 'Write comprehensive user guide.',
//            'project_id' => 1,
//            'due_date' => '2024-01-25',
//            'start_date' => '2024-01-10',
//            'estimated_start_date' => '2024-01-10',
//            'estimated_end_date' => '2024-01-17',
//            'estimated_time_inDays' => 7,
//            'actual_time_inDays' => 8,
//            'priority' => 1
//        ]);
//
//        App\Models\Tasks::create([
//            'title' => 'Security Audit',
//            'type' => 'back-end',
//            'description' => 'Perform vulnerability assessment.',
//            'project_id' => 1,
//            'due_date' => '2024-02-15',
//            'start_date' => '2024-02-01',
//            'estimated_start_date' => '2024-02-01',
//            'estimated_end_date' => '2024-02-11',
//            'estimated_time_inDays' => 10,
//            'actual_time_inDays' => 12,
//            'priority' => 5
//        ]);
//
//        App\Models\Tasks::create([
//            'title' => 'Deployment Preparation',
//            'type' => 'back-end',
//            'description' => 'Prepare deployment scripts and configurations.',
//            'project_id' => 1,
//            'due_date' => '2024-02-20',
//            'start_date' => '2024-02-10',
//            'estimated_start_date' => '2024-02-10',
//            'estimated_end_date' => '2024-02-15',
//            'estimated_time_inDays' => 5,
//            'actual_time_inDays' => 6,
//            'priority' => 3
//        ]);
//
//        return response()->json(['status' => 'success']);
//    });
