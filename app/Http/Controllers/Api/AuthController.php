<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    private string $rateLimitKey;

    public function __construct()
    {
        parent::__construct();
        $this->rateLimitKey = 'login_attempt_' . request()->ip();
    }
    public function login(Request $request)
    {
        $rateLimit = $this->rateLimit();
        if ($rateLimit) {
            return $rateLimit;
        }
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember', false);

        if (!auth()->attempt($credentials, $remember)) {
            $this->increaseRateLimit();
            return response()->error([], __('auth.failed'), 401);
        }

        $user = auth()->user();

        $token = $user->createToken('auth_token', ['role-' . $user->role->value])->plainTextToken;
        $this->clearRateLimit();

        return response()->success([
            'token' => $token,
            'refresh_token' => $remember ? $user->getRememberToken() : null,
            'expires_at' => time() + config('sanctum.token_expiration') * 60,
            'user' => UserResource::make($user),
            'role' => $user->role,
        ], __('auth.login.success'));
    }

    public function logout(Request $request)
    {
        $this->_USER?->currentAccessToken()->delete();
        return response()->success([], __('auth.login.logout'));
    }

    private function rateLimit()
    {
        if (RateLimiter::tooManyAttempts($this->rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($this->rateLimitKey);
            return response()->error([], __('auth.throttle', ['seconds' => $seconds]), 429);
        }
        return null;
    }

    private function increaseRateLimit()
    {
        if (RateLimiter::remaining($this->rateLimitKey, 5)) {
            RateLimiter::increment($this->rateLimitKey);
        }
    }

    private function clearRateLimit()
    {
        RateLimiter::clear($this->rateLimitKey);
    }
}
