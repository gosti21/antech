<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Auth\LoginRequest;
use App\Http\Requests\Api\v1\Auth\RegisterRequest;
use App\Http\Resources\Api\v1\Auth\LoginResource;
use App\Services\Api\v1\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $service
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->service->login($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'token' => $result['token'],
            'user' => new LoginResource($result['user']),
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->service->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada'
        ], 200);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->service->register($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'token' => $result['token'],
            'user' => new LoginResource($result['user']),
        ], 200);
    }
}
