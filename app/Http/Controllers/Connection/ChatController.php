<?php

namespace App\Http\Controllers\Connection;

use App\Events\MessageSent;
use App\Events\TeamChat;
use App\Events\TeamMessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Team;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function sendMessage(Request $request) : JsonResponse
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $team = null;
        $user = Auth::user();
        $role = $user->getRoleNames()->first();

        if ($role === 'leader') {
            $team = $user->ownedTeam;
            if (!$team) {
                return response()->json(['error' => 'Leader does not own any team'], 403);
            }
        } else {
            $team = $user->teams()->first();
            if (!$team) {
                return response()->json(['error' => 'User is not a member of any team'], 403);
            }
        }

        $message = Message::create([
            'team_id' => $team->id,
            'sender_id' => $user->id,
            'message' => $request->message
        ]);

        $user = Auth::user();
        $userGitHubAccount = $user->githubAccount;
        $avatarUrl = $userGitHubAccount && $userGitHubAccount->avatar
            ? asset(Storage::url($userGitHubAccount->avatar))
            : null;

        broadcast(new TeamMessageSent($team->id, $message, $user))->toOthers();

        return response()->json([
            'status' => 'Message sent successfully',
            'message' => [
                'id' => $message->id,
                'content' => $message->message,
                'created_at' => $message->created_at->toDateTimeString(),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $avatarUrl
                ]
            ]
        ]);
    }

    public function getMessages($teamId) : JsonResponse
    {
        $team = Team::findOrFail($teamId);
        if (!$team->members()->where('users.id', Auth::id())->exists()) {
            return response()->json(['error' => 'Unauthorized - Not a team member'], 403);
        }

        $messages = Message::with(['user', 'team'])
            ->where('team_id', $teamId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'content' => $message->message,
                    'created_at' => $message->created_at->toDateTimeString(),
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                        //'avatar' => $message->user->avatar_url ?? null
                    ],
                    'team' => [
                        'id' => $message->team->id,
                        'name' => $message->team->team_name
                    ]
                ];
            });

        return response()->json($messages);
    }

    public function getTeamMembers($teamId) : JsonResponse
    {
        $team = Team::findOrFail($teamId);

        if (!$team->members()->where('users.id', Auth::id())->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $members = $team->members()->get(['users.id', 'users.name', 'users.email', 'users.avatar_url']);

        return response()->json($members);
    }

}
