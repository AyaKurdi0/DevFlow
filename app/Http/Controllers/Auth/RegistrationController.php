<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class RegistrationController extends Controller
{
    ####################### Leader Registration #######################
    public function leaderRegister(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ]);

        try {
            $leader = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $role = Role::where('name', 'leader')->firstOrFail();
            $leader->assignRole($role);

            $leader->sendEmailVerificationNotification();
            event(new Registered($leader));

            $token = $leader->createToken('LeaderToken')->plainTextToken;

            return response()->json([
                'user' => $leader,
                'token' => $token,
            ]);
        }
        catch (Exception $exception){
            return response()->json([
                'message' => 'User Registration Failed',
                'error' => $exception->getMessage(),
            ]);
        }
    }


    ####################### User Login To The System #######################
    public function login(Request $request): Response|JsonResponse|Application|ResponseFactory
    {

        $credentials = $request->validate([
            'email' => 'required|email|string|exists:users,email',
            'password' => [
                'required',
            ],
            'remember' => 'boolean',
        ]);

        try {
            $remember = $credentials['remember'] ?? false;
            unset($credentials['remember']);

            if (!Auth::attempt($credentials, $remember)) {
                return response()->json([
                    'error' => 'the provided credentials are not correct',
                ], 422);
            }
            $user = Auth::user();
            $user->sendEmailVerificationNotification();

            $role = $user->getRoleNames()->first();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response([
                'user'=>$user,
                'role_name'=>$role,
                'token'=>$token,
            ]);
        }
        catch (Exception $exception){
            return response()->json([
                'message' => 'Login Failed',
                'error' => $exception->getMessage(),
            ]);
        }
    }


    ####################### User Logout From The System #######################
    public function logout(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'No authenticated user found',
                'success' => false,
            ], 401);
        }

        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out successfully',
                'success' => true,
            ], 200);
        }
        catch (Exception $exception){
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
