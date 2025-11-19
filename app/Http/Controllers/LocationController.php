<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $location = Location::all();
        return response()->json([
            'message' => 'Data Lokasi berhasil diambil !',
            'data' => $location
        ]);
    }

    public function show($id)
    {
        $location = Location::find($id);
        return response()->json([
            'message' => 'Data Lokasi berhasil diambil !',
            'data' => $location
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_name' => 'required|string|max:255',
        ]);

        $location = Location::create($validated);
        return response()->json([
            'message' => 'Data Lokasi berhasil disimpan !',
            'data' => $location
        ]);
    }

    public function update(Request $request, $id)
    {
        $location = Location::find($id);

        $validated = $request->validate([
            'location_name'    => 'required|string|max:255',
        ]);

        $location->update($validated);
        return response()->json([
            'message' => 'Data Lokasi berhasil diupdate !',
            'data' => $location
        ]);
    }

    public function destroy($id)
    {
        $location = Location::find($id);

        $location->delete();
        return response()->json([
            'message' => 'Data Lokasi berhasil dihapus !'
        ]);
    }
}
