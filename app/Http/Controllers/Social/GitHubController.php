<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Models\GitHubAccount;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class GitHubController extends Controller
{

    public function redirectToGitHub()
    {
        return Socialite::driver('github')->stateless()->redirect();
    }

    public function handleGitHubCallback(Request $request)
    {
        try {
            // التحقق من وجود code
            $code = $request->get('code');

            if (!$code) {
                return response()->json(['error' => 'Authorization code not provided'], 400);
            }

            // طلب access_token من GitHub
            $response = Http::asForm()->post('https://github.com/login/oauth/access_token', [
                'client_id' => env('GITHUB_CLIENT_ID'),
                'client_secret' => env('GITHUB_CLIENT_SECRET'),
                'code' => $code,
                'redirect_uri' => env('GITHUB_REDIRECT_URI'),
            ]);

            // تحويل النتيجة إلى مصفوفة
            parse_str($response->body(), $data);

            // التحقق من وجود access_token
            if (!isset($data['access_token'])) {
                Log::error('Failed to get access token', ['response' => $data]);
                return response()->json(['error' => 'Failed to get access token', 'details' => $data], 400);
            }

            $accessToken = $data['access_token'];

            // جلب بيانات المستخدم من GitHub
            $userResponse = Http::withHeaders([
                'Authorization' => "Bearer $accessToken",
                'Accept' => 'application/json'
            ])->get('https://api.github.com/user');

            $githubUser = $userResponse->json();

            // التحقق من وجود بيانات المستخدم
            if (!isset($githubUser['id'])) {
                Log::error('Failed to fetch user data', ['response' => $githubUser]);
                return response()->json(['error' => 'Failed to fetch user data', 'details' => $githubUser], 400);
            }

            // البحث عن حساب GitHub المرتبط
            $githubAcc = GitHubAccount::where('github_id', $githubUser['id'])->first();

            if (!$githubAcc) {
                // البحث عن مستخدم موجود باستخدام البريد الإلكتروني
                $user = User::where('email', $githubUser['email'])->first();

                if (!$user) {
                    // إنشاء مستخدم جديد إذا لم يكن موجودًا
                    $password = bcrypt(uniqid());
                    $user = User::create([
                        'name' => $githubUser['name'] ?? $githubUser['login'],
                        'email' => $githubUser['email'],
                        'password' => $password,
                    ]);
                }

                // إنشاء حساب GitHub جديد
                $githubAcc = GitHubAccount::create([
                    'user_id' => $user->id,
                    'github_id' => $githubUser['id'],
                    'avatar' => $githubUser['avatar_url'],
                    'token' => $accessToken,
                ]);
            } else {
                // استخدام المستخدم الموجود
                $user = $githubAcc->user;
            }

            // تسجيل دخول المستخدم
            Auth::login($user);

            // إنشاء token جديد
            $token = $user->createToken('github-auth')->plainTextToken;

            // إرجاع النتيجة
            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'githubAcc' => $githubAcc,
                'token' => $token
            ], 200);

        } catch (Exception $e) {
            // تسجيل الخطأ
            Log::error('GitHub authentication failed', ['error' => $e->getMessage()]);

            // إرجاع رسالة الخطأ
            return response()->json([
                'error' => 'Authentication Failed',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
