<?php

namespace App\Http\Controllers;

use App\Models\StatusBorrower;
use Illuminate\Http\Request;

class StatusBorrowerController extends Controller
{
    public function index()
    {
        $status = StatusBorrower::all();
        return response()->json([
            'message' => 'Data Status Peminjam berhasil diambil !',
            'data' => $status
        ]);
    }

    public function show($id)
    {
        $status = StatusBorrower::find($id);
        return response()->json([
            'message' => 'Data Status Peminjam berhasil diambil !',
            'data' => $status
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'status_name' => 'required|string|max:255',
        ]);

        $status = StatusBorrower::create($validated);
        return response()->json([
            'message' => 'Data Status Peminjam berhasil disimpan !',
            'data' => $status
        ]);
    }

    public function update(Request $request, $id)
    {
        $status = StatusBorrower::find($id);

        $validated = $request->validate([
            'status_name'    => 'required|string|max:255',
        ]);

        $status->update($validated);
        return response()->json([
            'message' => 'Data Status Peminjam berhasil diupdate !',
            'data' => $status
        ]);
    }

    public function destroy($id)
    {
        $status = StatusBorrower::find($id);

        $status->delete();
        return response()->json([
            'message' => 'Data Status Peminjam berhasil dihapus !'
        ]);
    }
}
