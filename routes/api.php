<?php

use App\Http\Controllers\Connection\ChatController;
use App\Http\Controllers\Developer\DeveloperReviewsController;
use App\Http\Controllers\Developer\DeveloperTasksController;
use App\Http\Controllers\Developer\DocumentsManageController;
use App\Http\Controllers\Leader\ManageProjectController;
use App\Http\Controllers\Leader\ManageReviewsController;
use App\Http\Controllers\Leader\ManageTasksController;
use App\Http\Controllers\Leader\ManageTeamController;
use App\Http\Controllers\Services\GitHubServicesController;
use App\Http\Controllers\Social\GitHubController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegistrationController::class, 'leaderRegister'])
    ->name('leader.register');

Route::post('/login', [RegistrationController::class, 'login'])
    //->middleware('verified')
    ->name('login');

Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::get('/auth/github', [GitHubController::class, 'redirectToGitHub']);
Route::get('/auth/github/callback', [GitHubController::class, 'handleGitHubCallback']);


//Route::get('/github/login', [GitHubController::class, 'goToGithub']);
//Route::get('/github/callback', [GitHubController::class, 'handleGitHubCallback']);

//Route::post('/leader/manageMember/addDeveloper',[ManageMemberController::class,'addDeveloper'])
//    ->middleware('permission:add team member')
//    ->name('leader.manageMember.addDeveloper');
//Route::middleware(['auth:sanctum'])->post('/leader/manageMember/addDeveloper', [ManageMemberController::class, 'addDeveloper'])
//    ->name('leader.manageMember.addDeveloper')
//    ->middleware(['auth:sanctum']);

//Route::post('/leader/manageTeam/createTeam',[ManageTeamController::class,'createTeam'])
//    //->middleware('permission:add team member')
//    ->name('leader.manageTeam.createTeam');




//              #########################################################################

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/leader/manageTeam/createTeam',[ManageTeamController::class,'createTeam'])
        ->name('leader.manageTeam.createTeam');

    Route::post('/leader/manageMember/addDeveloper',[ManageMemberController::class,'addDeveloper'])
        ->name('leader.manageMember.addDeveloper');

    Route::get('/leader/manageMember/deleteDeveloper/{id}',[ManageMemberController::class,'removeDeveloper'])
        ->name('leader.manageMember.removeDeveloper');

    Route::post('/leader/manageMember/assignPermission/{id}',[ManageMemberController::class,'assignPermission'])
        ->name('leader.manageMember.assignPermission');

    Route::get('/leader/manageMember/unsignedPermission/{id}',[ManageMemberController::class,'unsignedPermission'])
        ->name('leader.manageMember.unsignedPermission');

    Route::post('/leader/manageProject/createProject',[ManageProjectController::class,'createNewProject'])
        ->name('leader.manageProject.createProject');

    Route::get('/leader/manageProject/startingProject/{id}',[ManageProjectController::class,'startImplementProject'])
        ->name('leader.manageProject.startingProject');

    Route::get('/leader/manageProject/completeProject/{id}',[ManageProjectController::class,'endImplementProject'])
        ->name('leader.manageProject.completeProject');

    Route::get('/leader/manageProject/updateProject/{id}',[ManageProjectController::class,'updateProject'])
        ->name('leader.manageProject.updateProject');

    Route::get('/leader/manageProject/displayProjects',[ManageProjectController::class,'displayProjects'])
        ->name('leader.manageProject.displayProjects');

    Route::post('/leader/manageTask/createTask/{id}', [ManageTasksController::class, 'creatNewTask'])
        ->name('leader.manageTask.createTask');

    Route::post('/leader/manageTask/assignTask/{id}', [ManageTasksController::class, 'assignTask'])
        ->name('leader.manageTask.assignTask');

    Route::post('/leader/manageTask/deleteTask/{id}', [ManageTasksController::class, 'deleteTask'])
        ->name('leader.manageTask.deleteTask');

    Route::post('/leader/manageTask/unassignTask/{id}', [ManageTasksController::class, 'unassignTask'])
        ->name('leader.manageTask.unassignTask');

    Route::get('/leader/manageTask/displayTasks/{id}',[ManageTasksController::class,'displayTaskInfo'])
        ->name('leader.manageTask.displayTasks');

    Route::post('/leader/manageReview/approveTask/{id}', [ManageReviewsController::class, 'approveTask'])
        ->name('leader.manageTask.approveTask');

    Route::post('/leader/manageReview/rejectTask/{id}', [ManageReviewsController::class, 'rejectTask'])
        ->name('leader.manageTask.rejectTask');

    Route::post('/leader/manageReview/addComment/{id}', [ManageReviewsController::class, 'addComment'])
        ->name('leader.manageReview.addComment');

    Route::get('/leader/manageReview/displayAllReview', [ManageReviewsController::class, 'displayAllTasksToReview'])
        ->name('leader.manageReview.displayReview');

    Route::get('/leader/manageReview/displayUnreviewedTasks', [ManageReviewsController::class, 'displayUnreviewedTasks'])
        ->name('leader.manageReview.displayUnreviewedTasks');

    Route::get('/leader/manageReview/displayApprovedTasks', [ManageReviewsController::class, 'displayApprovedTasks'])
        ->name('leader.manageReview.displayApprovedTasks');

    Route::get('/leader/manageReview/displayRejectedTasks', [ManageReviewsController::class, 'displayRejectedTasks'])
        ->name('leader.manageReview.displayRejectedTasks');


});


//              #########################################################################

Route::middleware(['auth:sanctum'])->group(callback: function () {
    Route::get('/Developer/taskDisplaying/getDeveloperTasks',[DeveloperTasksController::class,'displayTasks'])
        ->name('Developer.taskDisplaying.getDeveloperTasks');

    Route::get('/Developer/taskDisplaying/getTaskInfo/{id}',[DeveloperTasksController::class,'displayTaskInfo'])
        ->name('Developer.taskDisplaying.getTaskInfo');

    Route::get('/Developer/taskUpdating/updateTaskStatus/{id}',[DeveloperTasksController::class,'updateTaskStatus'])
        ->name('Developer.taskUpdating.updateTaskStatus');

    Route::post('/Developer/documentManage/uploadFile/{id}', [DocumentsManageController::class, 'uploadFiles'])
        ->name('Developer.documentManage.uploadFile');

    Route::get('/Developer/documentManage/downloadFile/{id}',[DocumentsManageController::class, 'downloadFile'])
        ->name('Developer.documentManage.downloadFile');

    Route::get('/Developer/reviewDisplaying/getDeveloperReviews',[DeveloperReviewsController::class,'displayAllReview'])
        ->name('Developer.reviewDisplaying.getDeveloperReviews');

    Route::get('/Developer/reviewDisplaying/getApprovedReviews',[DeveloperReviewsController::class,'displayApprovedReviews'])
        ->name('Developer.reviewDisplaying.getApprovedReviews');

    Route::get('/Developer/reviewDisplaying/getRejectedReviews',[DeveloperReviewsController::class,'displayRejectedReviews'])
        ->name('Developer.reviewDisplaying.getRejectedReviews');
});




Route::middleware('auth:sanctum')->group(function() {

    Route::post('/Chat/sendMessage', [ChatController::class, 'sendMessage'])
        ->name('Chat.sendMessage');

    Route::get('/Chat/getMessages', [ChatController::class, 'getMessages'])
        ->name('Chat.getMessages');
});



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/github/profile', [GitHubServicesController::class, 'getUserProfile']);
    Route::get('/github/repositories', [GitHubServicesController::class, 'getUserRepositories']);
    Route::post('/github/connect', [GitHubServicesController::class, 'connectGitHub'])->name('github.connect');
});
