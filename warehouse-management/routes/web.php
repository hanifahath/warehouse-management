<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController; // Asumsikan ada DashboardController
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\RestockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController; // Atau Controller Login Anda

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan rute web untuk aplikasi Anda.
|
*/

// --- 0. AUTHENTICATION & DEFAULT ROUTES ---

// Ganti dengan routes otentikasi yang Anda gunakan (misalnya Breeze/Jetstream)
// Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

Route::get('/', function () {
    // Redirect ke dashboard setelah login
    return redirect()->route('dashboard');
})->middleware('auth');

Route::middleware('auth')->group(function () {
    // Dashboard adalah halaman utama setelah login
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// --- 1. PRODUCT MANAGEMENT (Akses: Admin, Manager) ---
// Digunakan oleh Admin dan Manager untuk CRUD barang
Route::middleware(['auth', 'role:Admin|Manager'])->group(function () {
    Route::resource('products', ProductController::class);
});

// --- 2. TRANSACTION MANAGEMENT (Semua Role terkait Gudang) ---
Route::middleware('auth')->group(function () {
    Route::prefix('transactions')->group(function () {
        // Index dan Show bisa dilihat semua role terkait (Admin/Manager/Staff)
        Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

        // Aksi Staff Gudang (Membuat Transaksi)
        Route::middleware('role:Admin|Manager|Staff')->group(function () {
            Route::get('/incoming/create', [TransactionController::class, 'createIncoming'])->name('transactions.create_incoming');
            Route::post('/incoming', [TransactionController::class, 'storeIncoming'])->name('transactions.store_incoming');
            
            Route::get('/outgoing/create', [TransactionController::class, 'createOutgoing'])->name('transactions.create_outgoing');
            Route::post('/outgoing', [TransactionController::class, 'storeOutgoing'])->name('transactions.store_outgoing');
        });

        // Aksi Manager (Approval Transaksi)
        Route::middleware('role:Admin|Manager')->group(function () {
            Route::post('/{transaction}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');
        });
    });
});

// --- 3. RESTOCK MANAGEMENT (Akses: Manager, Supplier, Staff) ---
Route::middleware('auth')->group(function () {
    Route::prefix('restock')->group(function () {
        Route::get('/', [RestockController::class, 'index'])->name('restock.index');
        
        // Aksi Manager (Membuat PO dan Menerima Barang)
        Route::middleware('role:Admin|Manager')->group(function () {
            Route::get('/create', [RestockController::class, 'create'])->name('restock.create');
            Route::post('/', [RestockController::class, 'store'])->name('restock.store');
            
            // Penerimaan Barang (Bisa dilakukan Manager atau Staff)
            Route::post('/{order}/receive', [RestockController::class, 'receive'])->middleware('role:Admin|Manager|Staff')->name('restock.receive');
        });

        // Aksi Supplier (Konfirmasi Order)
        Route::middleware('role:Supplier')->group(function () {
            Route::post('/{order}/confirm', [RestockController::class, 'confirm'])->name('restock.confirm');
        });
    });
});

// --- 4. USER/SUPPLIER MANAGEMENT (Akses: Admin) ---
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::resource('users', UserController::class)->only(['index', 'show']);

    // Route Khusus untuk Approval Supplier
    Route::post('/users/{user}/approve', [UserController::class, 'approveSupplier'])->name('users.approve_supplier');
});