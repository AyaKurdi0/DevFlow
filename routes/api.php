<?php

use App\Http\Controllers\Leader\ManageTeamController;
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

Route::get('/auth/github', [GitHubController::class, 'goToGithub'])
    ->name('social.redirect');

Route::get('/auth/callback', [GitHubController::class, 'handleGithubCallback'])
    ->name('/auth/github/callback');

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
});

