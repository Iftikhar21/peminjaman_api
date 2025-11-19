<?php

namespace App\Http\Controllers;

use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserDetailController extends Controller
{
    public function show(Request $request) {
        $user = $request->user();

        $userDetail = UserDetail::with('user', 'status', 'class', 'major')->where('user_id', $user->id)->first();
        return response()->json([
            'message' => 'Biodata Anda berhasil diambil !',
            'userDetail' => [
                'id' => $userDetail->id,
                'email' => $userDetail->user->email ?? null,
                'user_name' => $userDetail->user->name ?? null,
                'role' => $userDetail->user->role->role_name ?? null,
                'identity_number' => $userDetail->identity_number,
                'phone' => $userDetail->phone,
                'status' => $userDetail->status->status_name ?? null,
                'class' => $userDetail->class->class_name ?? null,
                'major' => $userDetail->major->major_name ?? null
            ]
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        // Buat validator manual supaya bisa kasih pesan custom
        $validator = Validator::make($request->all(), [
            'name'            => 'sometimes|string|max:255',
            'email'           => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'password'        => 'sometimes|string|min:6',
            'identity_number' => 'sometimes|string|max:255',
            'phone'           => 'sometimes|string|max:255',
            'status_id'       => 'sometimes|exists:status_borrower,id',
            'class_id'        => 'sometimes|exists:class,id',
            'major_id'        => 'sometimes|exists:major,id',
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

        // Update user
        $userUpdate = $request->only(['name', 'email', 'password']);
        if (isset($userUpdate['password'])) {
            $userUpdate['password'] = bcrypt($userUpdate['password']);
        }
        if (!empty($userUpdate)) {
            $user->update($userUpdate);
        }

        // Update user detail
        $user = $request->user();

        // Update user detail atau buat baru jika belum ada
        $userDetail = UserDetail::updateOrCreate(
            ['user_id' => $user->id],
            $request->only([
                'identity_number',
                'phone',
                'status_id',
                'class_id',
                'major_id'
            ])
        );

        $userDetail->load('user', 'status', 'class', 'major');

        return response()->json([
            'message' => 'Data berhasil diupdate!',
            'data' => [
                'id' => $userDetail->id,
                'email' => $userDetail->user->email,
                'user_name' => $userDetail->user->name,
                'role' => $userDetail->user->role->role_name,
                'identity_number' => $userDetail->identity_number,
                'phone' => $userDetail->phone,
                'status' => $userDetail->status->status_name ?? null,
                'class' => $userDetail->class->class_name ?? null,
                'major' => $userDetail->major->major_name ?? null,
            ]
        ]);
    }
}
