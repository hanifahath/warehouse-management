<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController; 
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RestockController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

// --- RUTE LANDING PAGE (ROOT /) ---
// Jika user belum login, tampilkan halaman welcome.
Route::get('/', function () {
    // Kita cek jika user sudah login, langsung ke dashboard
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

// --- PENTING: RUTE LOGOUT (Harus ada di luar middleware 'auth') ---
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');


Route::middleware(['auth', 'verified'])->group(function () {
    
    // Rute Profil Breeze yang sudah ada:
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // --- 1. DASHBOARD: Semua Role ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- 2. MANAGEMENT PENGGUNA: Hanya Admin ---
    Route::middleware(['role:Admin'])->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::patch('users/{user}/approve', [UserController::class, 'approveSupplier'])->name('users.approve_supplier');
    });

    // --- 3. PRODUCT & CATEGORY MANAGEMENT: Admin dan Manager ---
    Route::middleware(['role:Admin|Manager'])->group(function () {
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class)->except(['show']);
        
        // Opsi: Rute Laporan
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('inventory', function () { return view('reports.inventory'); })->name('inventory');
            Route::get('transactions', function () { return view('reports.transactions'); })->name('transactions');
            Route::get('low-stock', function () { return view('reports.low-stock'); })->name('low_stock');
        });
        
        // Approve/Reject Transaksi
        Route::patch('transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');
        Route::patch('transactions/{transaction}/reject', [TransactionController::class, 'reject'])->name('transactions.reject');
    });

    // --- 4. TRANSACTION MANAGEMENT: Semua Role Melihat. Staff Membuat. ---
    Route::prefix('transactions')->name('transactions.')->group(function () {
        // Melihat Transaksi - Semua Role
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        
        // Membuat Transaksi - Hanya Staff
        Route::middleware(['role:Staff'])->group(function () {
            Route::get('create-incoming', [TransactionController::class, 'createIncoming'])->name('create_incoming');
            Route::post('store-incoming', [TransactionController::class, 'storeIncoming'])->name('store_incoming');
            
            Route::get('create-outgoing', [TransactionController::class, 'createOutgoing'])->name('create_outgoing');
            Route::post('store-outgoing', [TransactionController::class, 'storeOutgoing'])->name('store_outgoing');
            
            // Edit & Delete Transaksi
            Route::get('{transaction}/edit', [TransactionController::class, 'edit'])->name('edit');
            Route::patch('{transaction}', [TransactionController::class, 'update'])->name('update');
            Route::delete('{transaction}', [TransactionController::class, 'destroy'])->name('destroy');
        });

        // Detail Transaksi - Harus di akhir
        Route::get('/{transaction}', [TransactionController::class, 'show'])->name('show');
    });
    
    // --- 5. RESTOCK / PURCHASE ORDER (PO) ---
    Route::prefix('restocks')->name('restocks.')->group(function () {
        
        // ** PENTING: Rute Statis HARUS di atas Rute Dinamis **
        
        // Admin/Manager: Buat PO (Rute Statis)
        Route::middleware(['role:Admin|Manager'])->group(function () {
            Route::get('create', [RestockController::class, 'create'])->name('create');
            Route::post('/', [RestockController::class, 'store'])->name('store');
        });
        
        // Semua Role melihat daftar PO (Rute Index)
        Route::get('/', [RestockController::class, 'index'])->name('index');
        
        // Supplier: Konfirmasi PO
        Route::middleware(['role:Supplier'])->group(function () {
            Route::patch('{restockOrder}/confirm', [RestockController::class, 'confirm'])->name('confirm');
        });
        
        // Admin/Manager/Staff: Terima Barang (Penerimaan Barang)
        Route::middleware(['role:Admin|Manager|Staff'])->group(function () {
            Route::patch('{restockOrder}/receive', [RestockController::class, 'receive'])->name('receive');
        });
        
        // Rute Dinamis (Detail PO) - Harus di akhir
        Route::get('/{restockOrder}', [RestockController::class, 'show'])->name('show');
    });
});

// ***************************************************************
// *** BARIS PENTING: Memuat rute otentikasi (login, register) ***
// ***************************************************************
require __DIR__.'/auth.php';