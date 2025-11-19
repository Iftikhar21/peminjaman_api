<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() {
        $product = Product::with('categories')->get();
        return response()->json([
            'message' => 'Data Product berhasil diambil !',
            'data' => $product
        ]);
    }

    public function show($id) {
        $product = Product::with('categories')->find($id);
        return response()->json([
            'message' => 'Data Product berhasil diambil !',
            'data' => $product
        ]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'qty' => 'required|integer',
        ]);

        $product = Product::create($validated);

        $product->load('category');

        return response()->json([
            'message' => 'Data Product berhasil disimpan !',
            'data' => $product
        ]);
    }

    public function update(Request $request, $id) {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'product_name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'qty' => 'sometimes|integer',
        ]);

        $product->update($validated);
        $product->load('category');

        return response()->json([
            'message' => 'Product berhasil diupdate!',
            'data' => $product
        ]);
    }

    public function destroy($id){
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json([
            'message' => 'Product berhasil dihapus!'
        ]);
    }
}
