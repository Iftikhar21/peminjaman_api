<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index() {
        $categories = Category::all();
        return response()->json([
            'message' => 'Data Categories berhasil diambil !',
            'data' => $categories
        ]);
    }

    public function show($id) {
        $categories = Category::find($id);
        return response()->json([
            'message' => 'Data Categories berhasil diambil !',
            'data' => $categories
        ]);
    }

    public function store(Request $request) {

        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        $categories = Category::create($validated);
        return response()->json([
            'message' => 'Data Categories berhasil disimpan !',
            'data' => $categories
        ]);
    }

    public function update(Request $request, $id) {
        $categories = Category::find($id);

        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        $categories->update($validated);
        return response()->json([
            'message' => 'Data Categories berhasil diupdate !',
            'data' => $categories
        ]);
    }

    public function destroy($id) {
        $categories = Category::find($id);
        $categories->delete();
        return response()->json([
            'message' => 'Data Categories berhasil dihapus !'
        ]);
    }
}
