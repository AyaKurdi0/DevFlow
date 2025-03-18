<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\GitHubAccount;
use App\Services\GitHubService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class GitHubServicesController extends Controller
{
    public function getUserProfile(): JsonResponse
    {
        try {
            $user = Auth::user();
            $gitHubAccount = GitHubAccount::where('user_id', $user->id)->first();

            if (!$gitHubAccount || !$gitHubAccount->github_token)
            {
                return response()->json([
                    'message' => 'Please link your GitHub account first',
                    'link_url' => route('services.github.login')
                ], 403);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $gitHubAccount->github_token,
                'Accept' => 'application/json',
            ])->get('https://api.github.com/user');

            return response()->json([
                'data' => $response->json(),
            ]);
        }
        catch (Exception $exception)
        {
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function getUserRepositories(): JsonResponse
    {
        try {
            $user = Auth::user();
            $githubAccount = GitHubAccount::where('user_id', $user->id)->first();

            if (!$githubAccount || !$githubAccount->github_token) {
                return response()->json([
                    'message' => 'Please link your GitHub account',
                    'link_url' => route('github.connect')
                ], 403);
            }

            $response = Http::withHeaders([
                'Authorization' => "token $githubAccount->github_token",
                'Accept' => 'application/vnd.github.v3+json',
            ])->get('https://api.github.com/user/repos');

            return response()->json($response->json());
        }
        catch (Exception $exception)
        {
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function connectGitHub(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $token = $request->input('github_token');

            $githubAccount = GitHubAccount::updateOrCreate(
                ['user_id' => $user->id],
                ['github_token' => $token]
            );

            return response()->json([
                'message' => 'GitHub account linked successfully',
                'github_account' => $githubAccount
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
