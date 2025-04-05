<?php

use App\Http\Controllers\Auth\GitHubServicesController;
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
use App\Http\Controllers\Social\GitHubController;
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

// GitHub Authentication Routes
Route::prefix('auth/github')->group(function () {
    Route::get('/', [GitHubController::class, 'redirectToGitHub'])
        ->name('github.redirect');
    Route::get('/callback', [GitHubController::class, 'handleGitHubCallback'])
        ->name('github.callback');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/status', [GitHubServicesController::class, 'status'])
            ->name('github.status');
        Route::delete('/unlink', [GitHubServicesController::class, 'unlink'])
            ->name('github.unlink');
        Route::post('/link', [GitHubServicesController::class, 'link'])
            ->name('github.link');
        Route::get('/check', [GitHubServicesController::class, 'check'])
            ->name('github.check');
        Route::delete('/disconnect', [GitHubServicesController::class, 'disconnect'])
            ->name('github.disconnect');
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

// Chat Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/teams/messages', [ChatController::class, 'sendMessage']);
    Route::get('/teams/{teamId}/messages', [ChatController::class, 'getMessages']);
    Route::get('/teams/{teamId}/members', [ChatController::class, 'getTeamMembers']);
});

