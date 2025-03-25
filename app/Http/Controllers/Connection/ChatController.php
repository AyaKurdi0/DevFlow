<?php

namespace App\Http\Controllers\Connection;

use App\Events\MessageSent;
use App\Events\TeamMessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Team;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function sendMessage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => 'required|string',
        ]);

        try {
            $sender = Auth::user();
            if (!$sender) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'User is not authenticated',
                ], 401);
            }

            $team = $sender->team()->first();
            if (!$team) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'User is not in a team',
                ], 400);
            }

            $message = Message::create([
                'sender_id' => $sender->id,
                'team_id' => $team->id,
                'message' => $data['message'],
            ]);
            
            broadcast(new TeamMessageSent($message, $sender))->toOthers();

            return response()->json([
                'status' => 'Message sent successfully',
                'message' => $message,
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                'status' => 'Error sending message',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function getMessages()
    {
        $user = Auth::user();
        $team = $user->team->get()->first();

        $message = Message::where('team_id', $team->id)->with('sender')->get();
        return response()->json($message);
    }
}
