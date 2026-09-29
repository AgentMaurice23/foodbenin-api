<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([

            'name' => $request->name,

            'phone' => $request->phone,

            'email' => $request->email,

            'password' => Hash::make(
                $request->password
            ),
        ]);

        $user->assignRole(
            RoleEnum::CLIENT->value
        );

        $token = $user->createToken(
            'mobile-token'
        )->plainTextToken;

        return response()->json([

            'user' => $user,

            'token' => $token
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        if (! Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {

            return response()->json([
                'message' => 'Identifiants invalides'
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken(
            'mobile-token'
        )->plainTextToken;

        return response()->json([

            'user' => $user,

            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request
            ->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json(
            $request->user()
        );
    }
}
