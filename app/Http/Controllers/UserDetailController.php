<?php

namespace App\Http\Controllers;

use App\Models\UserDetail;
use Illuminate\Http\Request;

class UserDetailController extends Controller
{
    public function show(Request $request) {
        $user = $request->user();

        $userDetail = UserDetail::with('user', 'status', 'class', 'major')->where('user_id', $user->id)->first();
        return response()->json([
            'message' => 'Biodata Anda berhasil diambil !',
            'userDetail' => $userDetail
        ]);
    }

    public function update(Request $request) {
        $user = $request->user();

        $validated = $request->validate([
            'identity_number' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'status_id' => 'required|exists:status_borrower,id',
            'class_id' => 'required|exists:class,id',
            'major_id' => 'required|exists:major,id',
        ]);

        $userDetail = UserDetail::updateOrCreate(['user_id' => $user->id], $validated);
        $userDetail->load('status', 'class', 'major');
        return response()->json([
            'message' => 'Biodata Anda berhasil diupdate !',
            'userDetail' => $userDetail
        ]);
    }
}
