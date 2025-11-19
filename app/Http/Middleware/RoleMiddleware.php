<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Pastikan user punya relasi role
        if (! $user->role) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Cek apakah role_name user ada di daftar role yang diperbolehkan
        if (! in_array($user->role->role_name, $roles)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}