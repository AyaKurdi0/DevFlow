<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ManageProfileController extends Controller
{

    ####################### Display User Information #######################
    public function getUserInfo(): JsonResponse
    {
        try {
            $user = Auth::user();
            $userGitHubAccount = $user->githubAccount()->first();

            if (!$user) {
                return response()->json([
                    'message' => 'No authenticated user found',
                ], 401);
            }

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $userGitHubAccount && $userGitHubAccount->avatar ? asset(Storage::url($userGitHubAccount->avatar)) : null,
                ],
                'message' => 'User info retrieved successfully',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to retrieve user info',
            ], 500);
        }
    }


    ####################### Update User Information #######################
    public function updateInfo(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'No authenticated user found',
                'success' => false
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'success' => false
            ], 422);
        }

        try {
            $user->update($request->only(['name', 'email']));

            $userGitHubAccount = $user->githubAccount()->first();
            if ($request->hasFile('avatar')) {
                if ($userGitHubAccount->avatar) {
                    Storage::delete($userGitHubAccount->avatar);
                }
                $avatarPath = $request->file('avatar')->store('public/avatars');
                $userGitHubAccount->update(['avatar' => $avatarPath]);
            }
            if ($request->hasFile('avatar')) {
                $userGitHubAccount->avatar = $request->file('avatar')->store('avatars');
                $user->save();
            }

            return response()->json([
                'message' => 'User info updated successfully',
                'user' => $user,
                'success' => true
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }


    ####################### Reset User Password #######################
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
        ]);
        try {
            $user = Auth::user();
            if (!Hash::check($request->current_password, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => 'Current password is incorrect',
                ]);
            }
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'user' => $user,
                'message' => 'Password reset successfully',
            ], 200);
        }
        catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }
}
