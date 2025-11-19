<?php

namespace App\Http\Controllers;

use App\Models\ClassBorrower;
use Illuminate\Http\Request;

class ClassBorrowerController extends Controller
{
    public function index() {
        $class = ClassBorrower::all();
        return response()->json([
            'message' => 'Data Class berhasil diambil !',
            'data' => $class
        ]);
    }

    public function show($id) {
        $class = ClassBorrower::find($id);
        return response()->json([
            'message' => 'Data Class berhasil diambil !',
            'data' => $class
        ]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'class_name' => 'required|string|max:255',
        ]);

        $class = ClassBorrower::create($validated);
        return response()->json([
            'message' => 'Data Class berhasil disimpan !',
            'data' => $class
        ]);

    }

    public function update(Request $request, $id){
        $class = ClassBorrower::find($id);

        $validated = $request->validate([
            'class_name'    => 'required|string|max:255',
        ]);
        
        $class->update($validated);
        return response()->json([
            'message' => 'Data Class berhasil diupdate !',
            'data' => $class
        ]);
    }

    public function destroy($id) {
        $class = ClassBorrower::find($id);

        $class->delete();
        return response()->json([
           'message' => 'Data Class berhasil dihapus !' 
        ]);
    }
}
