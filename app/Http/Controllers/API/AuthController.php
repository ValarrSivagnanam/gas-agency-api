<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Login
    public function login(Request $request)
    {
        // 1. Validate inputs
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Fetch user
        $user = User::where('email', $request->email)->first();

        // 3. Match credentials
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided gas agency credentials do not match.'],
            ]);
        }

        // 4. Generate Sanctum token specifying user permissions
        $token = $user->createToken('gas-agency-token', [$user->role])->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ], 200);
    }
}
