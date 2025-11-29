<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Supplier\RestockController;

    // --- RUTE LANDING PAGE (ROOT /) ---
    Route::get('/', function () {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return view('welcome');
    });

    // --- PENTING: RUTE LOGOUT ---
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::middleware(['auth', 'verified'])->group(function () {

    // --- PROFIL (Breeze) ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- DASHBOARD: Semua Role ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/api/products/{id}/price', [ProductController::class, 'getCostPrice'])->name('products.get_cost_price');

    // =========================
    // ADMIN ROUTES
    // =========================
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::patch('users/{user}/status', [\App\Http\Controllers\Admin\UserController::class, 'updateStatus'])
            ->name('users.update_status');

        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except(['show']);
    });

    // =========================
    // MANAGER ROUTES
    // =========================
    Route::middleware(['role:Manager'])->prefix('manager')->name('manager.')->group(function () {
        // Laporan sederhana
        Route::get('reports/inventory', [\App\Http\Controllers\Manager\ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('reports/transactions', [\App\Http\Controllers\Manager\ReportController::class, 'transactions'])->name('reports.transactions');
        Route::get('reports/low-stock', [\App\Http\Controllers\Manager\ReportController::class, 'lowStock'])->name('reports.low_stock');
        Route::get('transactions', [\App\Http\Controllers\Staff\TransactionController::class, 'index'])->name('transactions.index');

        // Approve / Reject transaksi
        Route::patch('transactions/{transaction}/approve', [\App\Http\Controllers\Manager\TransactionApprovalController::class, 'approve'])->name('transactions.approve');
        Route::patch('transactions/{transaction}/reject', [\App\Http\Controllers\Manager\TransactionApprovalController::class, 'reject'])->name('transactions.reject');

        // Manager juga bisa CRUD produk/kategori (opsional)
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->only(['index','create','store','edit','update']);
        Route::get('products/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'show'])->name('products.show');
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->only(['index','create','store','edit','update']);
    });

    // =========================
    // STAFF ROUTES
    // =========================
    Route::middleware(['role:Staff'])->prefix('staff')->name('staff.')->group(function () {
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('create-incoming', [\App\Http\Controllers\Staff\TransactionController::class, 'createIncoming'])->name('create_incoming');
            Route::post('store-incoming', [\App\Http\Controllers\Staff\TransactionController::class, 'storeIncoming'])->name('store_incoming');

            Route::get('create-outgoing', [\App\Http\Controllers\Staff\TransactionController::class, 'createOutgoing'])->name('create_outgoing');
            Route::post('store-outgoing', [\App\Http\Controllers\Staff\TransactionController::class, 'storeOutgoing'])->name('store_outgoing');

            Route::get('{transaction}/edit', [\App\Http\Controllers\Staff\TransactionController::class, 'edit'])->name('edit');
            Route::patch('{transaction}', [\App\Http\Controllers\Staff\TransactionController::class, 'update'])->name('update');
            Route::delete('{transaction}', [\App\Http\Controllers\Staff\TransactionController::class, 'destroy'])->name('destroy');

            // Semua role bisa lihat transaksi
            Route::get('/', [\App\Http\Controllers\Staff\TransactionController::class, 'index'])->name('index');
            Route::get('/{transaction}', [\App\Http\Controllers\Staff\TransactionController::class, 'show'])->name('show');
        });
    });

    // =========================
    // SUPPLIER ROUTES
    // =========================
    Route::middleware(['role:Supplier'])->prefix('supplier')->name('supplier.')->group(function () {
        Route::prefix('restocks')->name('restocks.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Supplier\RestockController::class, 'index'])->name('index');
            Route::get('/{restockOrder}', [\App\Http\Controllers\Supplier\RestockController::class, 'show'])->name('show');
            Route::patch('{restockOrder}/confirm', [\App\Http\Controllers\Supplier\RestockController::class, 'confirm'])->name('confirm');
        });
    });

    // =========================
    // RESTOCK (Admin/Manager/Staff)
    // =========================
    Route::prefix('restocks')->name('restocks.')->group(function () {
        Route::middleware(['role:Admin|Manager'])->group(function () {
            Route::get('create', [\App\Http\Controllers\Supplier\RestockController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Supplier\RestockController::class, 'store'])->name('store');
        });

        Route::get('/', [\App\Http\Controllers\Supplier\RestockController::class, 'index'])->name('index'); 

        Route::middleware(['role:Admin|Manager|Staff'])->group(function () {
            Route::patch('{restockOrder}/receive', [\App\Http\Controllers\Supplier\RestockController::class, 'receive'])->name('receive');
        });
    });
});

require __DIR__.'/auth.php';