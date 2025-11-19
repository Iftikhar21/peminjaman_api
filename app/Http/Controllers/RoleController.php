<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(){
        $role = Role::all();
        return response()->json(
            [
                'message' => 'Data Role berhasil diambil !',
                'data' => $role
            ]
        );
    }

    public function show($id) {
        $role = Role::find($id);
        return response()->json([
            'message' => 'Data Role berhasil diambil !',
            'data' => $role
        ]);
    }

    public function store(Request $request) {
        $role = Role::create($request->all());
        return response()->json([
            'message' => 'Data Role berhasil disimpan !',
            'data' => $role
        ]);
    }

    public function update(Request $request, $id) {
        $role = Role::find($id);
        $role->update($request->all());
        return response()->json([
            'message' => 'Data Role berhasil diupdate !',
            'data' => $role
        ]);
    }

    public function destroy($id) {
        $role = Role::find($id);
        $role->delete();
        return response()->json([
            'message' => 'Data Role berhasil dihapus !'
        ]);
    }
}
