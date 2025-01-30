<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{

    ####################### Email Need To Being Verified #######################
    public function notice()
    {
        try {
            return response()->json([
                'message' => 'Please verify your email address.'
            ], 403);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    ####################### Success Email Verify Process #######################
    public function verify(Request $request)
    {
        try {
            $request->fulfill();
            return response()->json([
                'message' => 'Email successfully verified.'
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    #######################  Email Verification Process #######################
    public function send(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Email already verified.', 400]);
            }

            $user->sendEmailVerificationNotification();
            return back()->with('message', 'Verification link sent!');
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
}
