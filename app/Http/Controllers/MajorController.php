<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    public function index()
    {
        $major = Major::all();
        return response()->json([
            'message' => 'Data Jurusan berhasil diambil !',
            'data' => $major
        ]);
    }

    public function show($id)
    {
        $major = Major::find($id);
        return response()->json([
            'message' => 'Data Jurusan berhasil diambil !',
            'data' => $major
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'major_name' => 'required|string|max:255',
        ]);

        $major = Major::create($validated);
        return response()->json([
            'message' => 'Data Jurusan berhasil disimpan !',
            'data' => $major
        ]);
    }

    public function update(Request $request, $id)
    {
        $major = Major::find($id);

        $validated = $request->validate([
            'major_name'    => 'required|string|max:255',
        ]);

        $major->update($validated);
        return response()->json([
            'message' => 'Data Jurusan berhasil diupdate !',
            'data' => $major
        ]);
    }

    public function destroy($id)
    {
        $major = Major::find($id);

        $major->delete();
        return response()->json([
            'message' => 'Data Jurusan berhasil dihapus !'
        ]);
    }
}
