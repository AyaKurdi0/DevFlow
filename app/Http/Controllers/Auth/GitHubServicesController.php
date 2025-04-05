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
                ->scopes(['read:user', 'user:email'])
                ->stateless()
                ->redirect()
                ->getTargetUrl()
        ]);
    }

    public function callback(Request $request): JsonResponse
    {
        try {
            $githubUser = Socialite::driver('github')->stateless()->user();
            $existingUser = User::whereHas('githubAccount', function ($query) use ($githubUser) {
                $query->where('github_id', $githubUser->getId());
            })->first();

            if ($existingUser) {
                $token = $existingUser->createToken('github_token')->plainTextToken;
                return response()->json([
                    'token' => $token,
                    'user' => $existingUser,
                    'is_new' => false
                ]);
            }

            $user = User::create([
                'name' => $githubUser->getName() ?? $githubUser->getNickname(),
                'email' => $githubUser->getEmail(),
                'password' => bcrypt(Str::random(24)),
            ]);

            GitHubAccount::create([
                'user_id' => $user->id,
                'github_id' => $githubUser->getId(),
                'username' => $githubUser->getNickname(),
                'token' => $githubUser->token,
                'avatar_url' => $githubUser->getAvatar(),
            ]);

            $token = $user->createToken('github_token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
                'is_new' => true
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'GitHub authentication failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function link(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            $response = Http::withHeaders([
                'Accept' => 'application/json'
            ])->post('https://github.com/login/oauth/access_token', [
                'client_id' => env('GITHUB_CLIENT_ID'),
                'client_secret' => env('GITHUB_CLIENT_SECRET'),
                'code' => $request->code,
                'redirect_uri' => env('GITHUB_LINK_REDIRECT_URI')
            ]);

            if ($response->failed()) {
                throw new Exception('Failed to exchange code for token');
            }

            $accessToken = $response->json()['access_token'];
            $githubUser = Socialite::driver('github')
                ->stateless()
                ->userFromToken($accessToken);
            $existingAccount = GitHubAccount::where('github_id', $githubUser->getId())->first();

            if ($existingAccount && $existingAccount->user_id !== $user->id) {
                return response()->json([
                    'error' => 'Account already linked',
                    'message' => 'This GitHub account is already linked to another user'
                ], 400);
            }

            $githubAccount = GitHubAccount::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'github_id' => $githubUser->getId(),
                    'username' => $githubUser->getNickname(),
                    'token' => $accessToken,
                    'avatar_url' => $githubUser->getAvatar()
                ]
            );

            return response()->json([
                'message' => 'GitHub account linked successfully',
                'account' => [
                    'username' => $githubAccount->username,
                    'avatar' => $githubAccount->avatar_url
                ]
            ]);

        } catch (Exception $e) {
            Log::error('GitHub linking error: ' . $e->getMessage());
            return response()->json([
                'error' => 'GitHub linking failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function check(Request $request): JsonResponse
    {
        $user = $request->user();
        $githubAccount = $user ? $user->githubAccount : null;

        return response()->json([
            'connected' => (bool)$githubAccount,
            'account' => $githubAccount ? [
                'username' => $githubAccount->username,
                'avatar' => $githubAccount->avatar_url
            ] : null
        ]);
    }

    public function disconnect(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $user->githubAccount) {
            $user->githubAccount()->delete();
            return response()->json(['message' => 'GitHub account disconnected']);
        }

        return response()->json(['error' => 'No GitHub account linked'], 400);
    }

//
//    public function getUserProfile(Request $request)
//    {
//        $user = $request->user();
//
//        if (!$user->githubAccount) {
//            return response()->json(['message' => 'GitHub account not linked'], 404);
//        }
//
//        try {
//            $githubData = $this->makeGitHubRequest('https://api.github.com/user', $user->githubAccount->github_token);
//            return response()->json($githubData);
//        } catch (Exception $e) {
//            return response()->json(['error' => $e->getMessage()], 500);
//        }
//    }
//
//    public function getUserRepositories(Request $request)
//    {
//        $user = $request->user();
//
//        if (!$user->githubAccount) {
//            return response()->json(['message' => 'GitHub account not linked'], 404);
//        }
//
//        try {
//            $repositories = $this->makeGitHubRequest('https://api.github.com/user/repos', $user->githubAccount->github_token);
//            return response()->json($repositories);
//        } catch (Exception $e) {
//            return response()->json(['error' => $e->getMessage()], 500);
//        }
//    }
//
//    private function makeGitHubRequest($url, $token)
//    {
//        $client = new \GuzzleHttp\Client();
//        $response = $client->get($url, [
//            'headers' => [
//                'Authorization' => "Bearer $token",
//                'Accept' => 'application/vnd.github.v3+json',
//            ],
//        ]);
//
//        return json_decode($response->getBody()->getContents(), true);
//    }
}
