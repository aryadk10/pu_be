<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user(); // Ambil data pengguna yang sedang login
        $user->password = Hash::make($request->password);
        $user->save();

        // Kembalikan response
        return response()->json([
            'message' => 'Password updated successfully!',
            'data' => $user
        ], 200);

    }
    public function update(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'lastname' => 'sometimes|string|max:255',
            'gender' => 'sometimes|string',
            'email' => 'sometimes|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'sometimes|string|max:15',
            'profile_picture' => 'image|mimes:jpg,jpeg,png,bmp|max:2048'
        ]);

        $user = Auth::user(); // Ambil data pengguna yang sedang login

        // Update data pengguna
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('lastname')) {
            $user->lastname = $request->lastname;
        }

        if ($request->has('gender')) {
            $user->gender = $request->gender;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->has('profile_picture')) {
            $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $imagePath;
        }

        // Simpan perubahan ke database
        $user->save();

        // Kembalikan response
        return response()->json([
            'message' => 'Profile updated successfully!',
            'data' => $user
        ], 200);
    }

    // Fungsi untuk mendapatkan data profil pengguna yang sedang login
    public function getUser(Request $request)
    {
        // Ambil data pengguna yang sedang login
        $user = Auth::user();

        // Kembalikan response dengan data pengguna
        return response()->json([
            'message' => 'User data retrieved successfully!',
            'data' => $user
        ], 200);
    }
}
