<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use GuzzleHttp\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;

use Illuminate\Validation\ValidationException;

class AuthController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['register', 'login']),
        ];
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse('User Registered Successfully', [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }


    public function login(LoginRequest $request): JsonResponse
    {
        // Auth Attempt 
        if (! Auth::attempt($request->validated())) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials',
            ]);
        }

        // optional void try catch if needed # $user = User::where('email', $request->email)->firstOrFail();
        try {
            $user = User::where('email', $request->email)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse('User Logged In Successfully', [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse('Successfully logged out', null);
    }
}
