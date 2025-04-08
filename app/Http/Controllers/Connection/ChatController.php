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
    ####################### Send Message Between The Team Member #######################
    public function sendMessage(Request $request): JsonResponse
    {
        try {
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
            $message->load('sender.githubAccount');
            broadcast(new TeamMessageSent($message))->toOthers();
            return response()->json(['status' => 'Message sent successfully']);
        }
        catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    ####################### Display Messages History #######################
    public function getAllMessages(): JsonResponse
    {
        try {
            $user = Auth::user();
            $team = $user->ownedTeam ?? $user->teams->first();

            if (!$team) {
                return response()->json(['error' => 'You are not a member of any team'], 403);
            }
            $messages = Message::with('sender.githubAccount')
                ->where('team_id', $team->id)
                ->latest()
                ->get();

            $formattedMessages = $messages->map(function ($message) use ($user) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'created_at' => $message->created_at->toDateTimeString(),
                    'is_sent_by_me' => $message->sender_id === $user->id,
                    'sender' => [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                        'email' => $message->sender->email,
                        'github_avatar' => $message->sender_avatar, // من الـ accessor
                    ],
                ];
            });

            return response()->json($formattedMessages);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve messages: ' . $e->getMessage()], 500);
        }
    }
}
