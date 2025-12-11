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
    
        if (strtolower(trim($user->role)) !== 'supplier') {
            return $next($request);
        }
    
        if ($user->is_approved) {
            return $next($request);
        }

        $allowedRoutes = ['supplier.pending', 'logout', 'login', 'register'];
        
        if ($request->routeIs($allowedRoutes)) {
            return $next($request);
        }
  
        return redirect()->route('supplier.pending')
            ->with('info', 'Your supplier account is pending admin approval.');
    }
}