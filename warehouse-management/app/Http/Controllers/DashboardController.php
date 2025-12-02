<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Delegasi semua logika ke service
        $data = $this->dashboardService->getDashboardData($user);

        return view('dashboard', $data);
    }
}
