<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SocialLoginRequest;
use App\Http\Resources\UserResource;
use App\Services\SocialAuthService;
use Illuminate\Http\JsonResponse;

class SocialAuthController extends Controller
{
    protected SocialAuthService $socialAuthService;

    public function __construct(SocialAuthService $socialAuthService)
    {
        $this->socialAuthService = $socialAuthService;
    }

    /**
     * Handle social login request.
     */
    public function socialLogin(SocialLoginRequest $request): JsonResponse
    {
        $result = $this->socialAuthService->handleSocialLogin($request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Login successfully',
            'token' => $result['token'],
            'user' => new UserResource($result['user']),
        ], 200);
    }
}
