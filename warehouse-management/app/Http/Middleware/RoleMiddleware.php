<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // Jika belum login, redirect ke login (web) atau return 401 (API)
        if (!$user) {
            if ($request->expectsJson()) {
                abort(401);
            }
            return redirect()->guest(route('login'));
        }

        // Flatten roles (support "Admin|Manager" or multiple params)
        $allowed = [];
        foreach ($roles as $r) {
            foreach (preg_split('/[|,]/', $r) as $p) {
                $p = trim($p);
                if ($p !== '') $allowed[] = strtolower($p);
            }
        }
        $allowed = array_unique($allowed);

        // Normalisasi role user
        $userRole = strtolower($user->role ?? '');

        if (in_array($userRole, $allowed, true)) {
            return $next($request);
        }

        // Tidak diizinkan: kembalikan respons sesuai konteks
        if ($request->expectsJson()) {
            abort(403);
        }

        abort(403, 'Akses Ditolak. Dibutuhkan role: ' . implode(' atau ', $allowed));
    }
}