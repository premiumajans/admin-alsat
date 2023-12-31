<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\User;
use Exception;
//use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\{
    Auth,
    Hash,
    Mail,
    Validator,
};
use Tymon\JWTAuth\Exceptions\{
    TokenBlacklistedException,
    JWTException,
    TokenExpiredException,
};
use Tymon\JWTAuth\Facades\JWTAuth;

class UserService
{
    public function login(array $credentials): \Illuminate\Http\JsonResponse
    {
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
       // $hasCompany = $user->company()->exists();
        $token = JWTAuth::fromUser($user);
        return response()->json([
            'user' => $user,
           // 'company' => $hasCompany,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ], 200);
    }

    public function refresh(): \Illuminate\Http\JsonResponse
    {
        try {
            $token = JWTAuth::parseToken()->refresh();
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'token_expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'token_invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'token_absent'], 401);
        }
        $user = JWTAuth::user();
        return response()->json([
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'company' => '',
                'type' => 'bearer',
            ]
        ], 200);
    }
    public function register(array $data): \Illuminate\Http\JsonResponse
    {
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->current_ad_count = 1;
        $user->password = Hash::make($data['password']);
        $user->save();
        $token = JWTAuth::fromUser($user);
        return response()->json([
            'user' => $user,
            'company' => false,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ], 200);
    }

    public function forgotPassword(string $email): array
    {
        if (!User::where('email', $email)->exists()) {
            return [
                'status' => 'error',
                'message' => 'user-not-found',
                'statusCode' => 404,
            ];
        }
        $user = User::where('email', $email)->first();
        $user->reset_token = md5($email);
        $user->save();
        $data = [
            'name' => $user->name,
            'email' => $email,
            'reset_token' => $user->reset_token,
        ];
        Mail::send('backend.mail.forget-password', $data, function ($message) use ($email) {
            $message->to($email);
            $message->subject(__('backend.confirm-your-password'));
        });
        return [
            'status' => 'success',
            'data' => [
                'token' => $user->reset_token,
                'email' => $user->email,
            ],
            'statusCode' => 200,
        ];
    }

    public function resetPassword(array $data): \Illuminate\Http\JsonResponse
    {
        if (!User::where('email', $data['email'])->exists()) {
            return response()->json([
                'message' => 'email-not-found',
            ], 500);
        }
        $user = User::where('email', $data['email'])->first();
        if ($data['token'] !== $user->reset_token) {
            return response()->json([
                'message' => 'token-is-not-match-email',
            ], 500);
        }
        $validator = Validator::make($data, [
            'new_password' => 'required|string',
            'confirm_password' => 'required|string|same:new_password',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'password-validation-failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        $user->password = Hash::make($data['new_password']);
        $user->reset_token = null;
        $user->save();
        return response()->json([
            'message' => 'password-updated-successfully',
        ], 200);
    }

    public function changePassword($request): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        if (!$request->has('current_password')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors(),
                ], 422);
            }
            $user->name = $request->input('name');
            $user->save();
            return response()->json([
                'message' => 'name-changed-successfully',
            ], 200);
        } else {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|different:current_password',
                'confirm_password' => 'required|string|same:new_password',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors(),
                ], 422);
            }
            if (!Hash::check($request->input('current_password'), $user->password)) {
                return response()->json([
                    'message' => 'current-password-mismatch',
                ], 401);
            }
            $user->password = Hash::make($request->input('new_password'));
            $user->save();
            return response()->json([
                'message' => 'password-changed-successfully',
            ], 200);
        }
    }

    /**
     * Check the user based on the provided JWT token.
     *
     * @param string $token
     * @return mixed|null
     */
    public function checkUser($token)
    {
        try {
            // Set the token on the JWTAuth facade
            JWTAuth::setToken($token);

            // Get the authenticated user using the admin guard
            if (!$user = Auth::guard('admin')->user()) {
                // User not found or not authenticated
                return null;
            }

            // User exists and is authenticated
            return $user;
        } catch (TokenExpiredException $e) {
            // Token has expired
            return null;
        } catch (JWTException $e) {
            // JWT validation failed
            return null;
        }
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        Auth::logout();
        return response()->json([
            'message' => 'logged-out-successfully',
        ], 200);
    }
}
