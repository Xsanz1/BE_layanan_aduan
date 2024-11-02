<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Login menggunakan username
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Find user by username
        $user = User::where('username', $request->username)->first();

        // Validate user and password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Password Salah'], 401);
        }

        // Create a token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }


    // Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    public function getProfile()
    {
        $user = Auth::user();

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'role' => $user->role,
            'foto' => $user->foto, // Assume this is the file path to the user's profile photo
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|string|unique:users,username,' . $user->id,
            'role' => 'sometimes|string',
            'foto' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle file upload if a new file is provided
        if ($request->hasFile('foto')) {
            if ($user->foto && file_exists(public_path('storage/' . $user->foto))) {
                unlink(public_path('storage/' . $user->foto));
            }
            $file = $request->file('foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/uploads'), $fileName);
            $user->foto = 'uploads/' . $fileName;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->role = $request->role ?? $user->role;

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user,
        ], 200);
    }
}
