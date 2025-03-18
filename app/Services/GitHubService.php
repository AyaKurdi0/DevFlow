<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GitHubService
{
    public function getUserRepositories($token)
    {
        $response = Http::withHeaders([
            'Authorization' => "token $token",
            'Accept' => 'application/vnd.github.v3+json',
        ])->get('https://api.github.com/user/repos');

        return $response->json();
    }

    public function getUserProfile($token)
    {
        $response = Http::withHeaders([
            'Authorization' => "token $token",
            'Accept' => 'application/vnd.github.v3+json',
        ])->get('https://api.github.com/user');

        return $response->json();
    }
}
