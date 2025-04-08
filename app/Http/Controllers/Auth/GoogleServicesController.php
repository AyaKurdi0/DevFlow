<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\GoogleUser;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleServicesController extends Controller
{
    public function redirect(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('google')
                ->scopes(['profile', 'email'])
                ->stateless()
                ->redirect()
                ->getTargetUrl()
        ]);
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $googleAccount = GoogleUser::where('google_id', $googleUser->getId())->first();

            if ($googleAccount) {
                $user = $googleAccount->user;
            } else {
                $user = User::where('email', $googleUser->getEmail())->first();

                if (!$user) {
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                    ]);
                }

                $googleAccount = GoogleUser::create([
                    'user_id' => $user->id,
                    'google_id' => $googleUser->getId(),
                    'token' => $googleUser->token,
                    'data' => json_encode($googleUser->user),
                ]);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return redirect(config('app.frontend_url') . "?token={$token}");

        } catch (Exception $e) {
            return response()->json(['error' => 'Authorization failed'], 401);
        }
    }
}
