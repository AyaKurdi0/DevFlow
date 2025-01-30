<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GitHubController extends Controller
{
    public function goToGithub()
    {
        try {
            $url = Socialite::driver('github')->stateless()->redirect()->getTargetUrl();
            return redirect($url);
        }
        catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function handleGithubCallback()
    {
        try {
            $gitHubUser = Socialite::driver('github')->user();

            $user = User::updateOrCreate([
                'github_id' => $gitHubUser->id,
            ], [
                'name' => $gitHubUser->name,
                'email' => $gitHubUser->email,
                'github_token' => $gitHubUser->token,
                'github_refresh_token' => $gitHubUser->refreshToken,
            ]);

//        Auth::login($user);
//        return redirect()->route('dashboard');
            $token = $user->createToken('githubToken')->plainTextToken;
            Auth::login($user);

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'error' => 'Authentication Failed',
                'message' => $exception->getMessage(),
            ],401);
        }
    }
}
