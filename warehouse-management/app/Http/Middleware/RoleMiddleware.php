<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. Jika tidak ada role yang diperlukan, izinkan
        if (empty($roles)) {
            return $next($request);
        }

        // 3. Parse roles dari format "Admin|Manager|Staff"
        // Laravel akan pass sebagai array ['Admin|Manager|Staff']
        // Kita perlu explode jadi ['Admin', 'Manager', 'Staff']
        
        $allowedRoles = [];
        foreach ($roles as $role) {
            // Split by pipe
            $splitRoles = explode('|', $role);
            foreach ($splitRoles as $r) {
                $allowedRoles[] = trim($r);
            }
        }
        
        // Remove duplicates
        $allowedRoles = array_unique($allowedRoles);

        // 4. Log untuk debug
        Log::info('=== ROLE MIDDLEWARE ===', [
            'url' => $request->url(),
            'user_role' => $user->role,
            'allowed_roles' => $allowedRoles,
            'match' => in_array($user->role, $allowedRoles) ? 'YES' : 'NO'
        ]);

        // 5. Cek apakah role user ada di daftar yang diizinkan
        if (in_array($user->role, $allowedRoles, true)) {
            return $next($request);
        }

        // 6. Jika tidak diizinkan
        Log::warning('Access denied', [
            'user' => $user->email,
            'role' => $user->role,
            'required' => $allowedRoles
        ]);

        abort(403, 'Akses Ditolak! Role Anda: "' . $user->role . '" | Dibutuhkan: ' . implode(' atau ', $allowedRoles));
    }
}