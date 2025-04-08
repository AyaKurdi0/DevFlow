<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\GitHubAccount;
use App\Models\User;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GitHubServicesController extends Controller
{
    public function redirect(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('github')
                ->scopes(['read:user', 'user:email','repo'])
                ->stateless()
                ->redirect()
                ->getTargetUrl()
        ]);
    }
    public function callback(Request $request)
    {
        $githubUser = Socialite::driver('github')->stateless()->user();

        $user = User::where('email', $githubUser->email)->firstOrFail();

        $user->gitHubAccount()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'github_id' => $githubUser->id,
                'token' => $githubUser->token,
                'data' => json_encode($githubUser->user),
            ]
        );

        $token = $user->createToken('api-token')->plainTextToken;

        return redirect(config('app.frontend_url') . "?token={$token}");
    }
    public function getUserRepositories(Request $request)
    {
        $user = $request->user();

        if (!$user->gitHubAccount) {
            return response()->json(['error' => 'GitHub account not connected'], 403);
        }

        $response = Http::withToken($user->gitHubAccount->token)
            ->get('https://api.github.com/user/repos');

        return response()->json($response->json());
    }

}
