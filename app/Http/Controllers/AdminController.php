<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $admin = Admin::with('user')->where('user_id', $user->id)->first();
        return response()->json([
            'message' => 'Biodata Anda (Admin) berhasil diambil !',
            'data' => [
                'id' => $admin->id,
                'email' => $admin->user->email ?? null,
                'user_name' => $admin->user->name ?? null,
                'role' => $admin->user->role->role_name ?? null,
                'phone' => $admin->phone
            ]
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
            'phone'    => 'sometimes|string|max:255',
        ], [
            'email.unique' => 'Email sudah digunakan, tidak bisa diupdate.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $userData = $request->only(['name', 'email', 'password']);
        if (isset($userData['password'])) {
            $userData['password'] = bcrypt($userData['password']);
        }
        if (!empty($userData)) {
            $user->update($userData);
        }

        $admin = Admin::firstOrCreate(['user_id' => $user->id]);
        if ($request->has('phone')) {
            $admin->phone = $request->phone;
            $admin->save();
        }

        $admin->load('user');

        return response()->json([
            'message' => 'Data berhasil diupdate!',
            'data' => [
                'id'        => $admin->id,
                'email'     => $admin->user->email,
                'user_name' => $admin->user->name,
                'role'      => $admin->user->role->role_name,
                'phone'     => $admin->phone
            ]
        ]);
    }
}
