<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function notice()
    {
        return response()->json(['message' => 'Please verify your email address.'], 403);
    }

    public function verify(Request $request)
    {
        $request->fulfill();
        return response()->json(['message' => 'Email successfully verified.']);
    }

    public function send(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.', 400]);
        }

        $user->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    }
}
