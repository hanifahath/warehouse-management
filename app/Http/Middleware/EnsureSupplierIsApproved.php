<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSupplierIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Jika user BUKAN supplier, lanjutkan
        if (strtolower(trim($user->role)) !== 'supplier') {
            return $next($request);
        }
        
        // Jika user adalah supplier DAN sudah approved, lanjutkan
        if ($user->is_approved) {
            return $next($request);
        }
        
        // Jika supplier TAPI belum approved
        // Cek jika sudah di halaman pending, jangan redirect loop
        $allowedRoutes = ['supplier.pending', 'logout', 'login', 'register'];
        
        if ($request->routeIs($allowedRoutes)) {
            return $next($request);
        }
        
        // Redirect ke pending page
        return redirect()->route('supplier.pending')
            ->with('info', 'Your supplier account is pending admin approval.');
    }
}