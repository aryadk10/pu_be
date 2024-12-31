<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Coba autentikasi
        if (Auth::attempt($credentials)) {
            // Regenerate session untuk keamanan
            // $request->session()->regenerate();
            $user = User::find(Auth::user()->id);
            $token = $user->createToken('mobile_token')->plainTextToken;

            // $existingToken = PersonalAccessToken::where('tokenable_id', Auth::user()->id)->first();
            // if(!$existingToken){
            //     return response()->json([
            //         'message' => 'Token not found, please contact administrator',
            //     ], 401);
            // }

            return response()->json([
                'message' => 'Login successful',
                'data' => [
                    'user' => Auth::user(),
                    'access-token' => $token
                ]
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid email or password',
        ], 401);
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        // Logout pengguna
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }
}
