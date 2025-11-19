<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if (User::where('email', $validated['email'])->exists()) {
            return response()->json([
                'message' => 'Email sudah terdaftar di database'
            ], 400);
        }

        $role = Role::where('role_name', 'user')->first();

        if (! $role) {
            return response()->json([
                'message' => 'Role user belum dibuat di tabel role'
            ], 400);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role_id' => $role->id,
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil!',
            'user' => $user
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'message' => 'Email tidak terdaftar.'
            ], 404);
        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Password salah.'
            ], 401);
        }

        // $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil!',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout berhasil!'
        ]);
    }
    
    public function index()
    {
        $user = User::with('role', 'admin', 'userDetail')->get();
        return response()->json([
            'message' => 'Data User berhasil diambil !',
            'user' => $user
        ]);
    }

    public function show($id)
    {
        $user = User::with('role')->find($id);
        return response()->json([
            'message' => 'Data User berhasil diambil !',
            'user' => $user
        ]);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:role,id', // pastikan role ID valid
        ]);

        if (User::where('email', $validated['email'])->exists()) {
            return response()->json([
                'message' => 'Email sudah terdaftar di database'
            ], 400);
        }

        // Buat user baru sesuai role_id yang dikirim admin
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role_id' => $validated['role_id'],
        ]);
    
        $user->load('role');
    
        return response()->json([
            'message' => 'User berhasil dibuat!',
            'user' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:6',
            'role_id' => 'sometimes|exists:role,id',
        ]);

        if (User::where('email', $validated['email'])->exists()) {
            return response()->json([
                'message' => 'Email sudah terdaftar di database'
            ], 400);
        }

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User berhasil diupdate!',
            'user' => $user
        ]);
    }

    public function destroy($id){
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            'message' => 'User berhasil dihapus!'
        ]);
    }

}
