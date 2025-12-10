<?php

use Illuminate\Support\Facades\Route;

// ============================================================
// CONTROLLERS
// ============================================================
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestockOrderController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ReportController;

// ============================================================
// MODEL CLASSES FOR POLICY
// ============================================================
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Transaction;
use App\Models\RestockOrder;
use App\Models\StockMovement;

// ============================================================
// PUBLIC ROUTES
// ============================================================

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
})->name('welcome');

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// Supplier pending page - accessible by unapproved suppliers
Route::get('/pending', function () {
    $user = auth()->user();
    
    // Jika tidak login, redirect ke login
    if (!$user) {
        return redirect()->route('login');
    }
    
    // Hanya supplier yang belum approved boleh akses
    if ($user->role !== 'supplier') {
        return redirect()->route('dashboard');
    }
    
    // Jika supplier sudah approved, redirect ke supplier dashboard
    if ($user->is_approved) {
        return redirect()->route('supplier.restocks.index');
    }
    
    return view('supplier.pending');
})->name('supplier.pending')->middleware('auth');


// routes/web.php
Route::post('/test-restock-simple', function(Request $request) {
    \Log::info('Test restock with data:', $request->all());
    
    // Check what's being sent
    echo "<pre>";
    echo "Product IDs: ";
    print_r($request->input('product_id', []));
    echo "\nQuantities: ";
    print_r($request->input('quantity', []));
    echo "</pre>";
    
    // Test validation
    $validator = Validator::make($request->all(), [
        'product_id' => 'required|array|min:1',
        'product_id.*' => 'required|integer|exists:products,id',
        'quantity' => 'required|array|min:1',
        'quantity.*' => 'required|integer|min:1',
    ]);
    
    if ($validator->fails()) {
        echo "Validation failed: ";
        print_r($validator->errors()->toArray());
    } else {
        echo "Validation passed!";
    }
})->middleware('auth');


// ============================================================
// AUTHENTICATED ROUTES
// Policy akan handle semua authorization checks
// ============================================================

Route::middleware(['auth', 'verified', 'supplier.approved'])->group(function () {
    
    // ========================================
    // DASHBOARD
    // ========================================
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ========================================
    // PROFILE (All Roles)
    // ========================================
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // ========================================
    // SUPPLIER ROUTES (Specific for suppliers)
    // ========================================
    // Route::middleware(['supplier.approved'])->group(function () {
        // Supplier view only their restock orders
        Route::get('/supplier/restocks', [RestockOrderController::class, 'supplierIndex'])
            ->name('supplier.restocks.index');

    // ========================================
    // PRODUCTS (Admin & Manager via ProductPolicy)
    // ========================================
    Route::prefix('products')->name('products.')->group(function () {
        // Index & Show - semua role yang authorized
        Route::get('/', [ProductController::class, 'index'])
            ->name('index')
            ->middleware('can:viewAny,' . Product::class);
        
        // Create, Update - Admin & Manager only
        Route::middleware('can:create,' . Product::class)->group(function () {
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
        });
        
        Route::middleware('can:update,product')->group(function () {
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::post('/{product}/adjust-stock', [ProductController::class, 'adjustStock'])
                ->name('adjust-stock');
        });
        
        // Delete - Admin & Manager (sesuai requirements)
        Route::delete('/{product}', [ProductController::class, 'destroy'])
            ->name('destroy')
            ->middleware('can:delete,product');

        // Show route (placed after more specific routes so '/create' and '/{product}/edit' are matched first)
        Route::get('/{product}', [ProductController::class, 'show'])
            ->name('show')
            ->middleware('can:view,product');
    });

    // ========================================
    // CATEGORIES (Admin & Manager via CategoryPolicy)
    // ========================================
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])
            ->name('index')
            ->middleware('can:viewAny,' . Category::class);
        
        Route::get('/{category}', [CategoryController::class, 'show'])
            ->name('show')
            ->middleware('can:view,category');
        
        Route::middleware('can:create,' . Category::class)->group(function () {
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
        });
        
        Route::middleware('can:update,category')->group(function () {
            Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        });
        
        Route::delete('/{category}', [CategoryController::class, 'destroy'])
            ->name('destroy')
            ->middleware('can:delete,category');
    });

    // ========================================
    // TRANSACTIONS (Multi-role: Staff, Manager, Supplier)
    // ========================================
    Route::prefix('transactions')->name('transactions.')->group(function () {
        // Index - semua role yang authorized (dengan filter di controller)
        Route::get('/', [TransactionController::class, 'index'])
            ->name('index')
            ->middleware('can:viewAny,' . Transaction::class);
        
        Route::get('/{transaction}', [TransactionController::class, 'show'])
            ->name('show')
            ->middleware('can:view,transaction');
        
        // ===== STAFF ONLY ROUTES =====
        Route::middleware('can:create,' . Transaction::class)->group(function () {
            Route::get('/create/incoming', [TransactionController::class, 'createIncoming'])
                ->name('create.incoming');
            Route::post('/incoming', [TransactionController::class, 'storeIncoming'])
                ->name('store.incoming');
            
            Route::get('/create/outgoing', [TransactionController::class, 'createOutgoing'])
                ->name('create.outgoing');
            Route::post('/outgoing', [TransactionController::class, 'storeOutgoing'])
                ->name('store.outgoing');
        });
        
        Route::middleware('can:update,transaction')->group(function () {
            Route::get('/{transaction}/edit', [TransactionController::class, 'edit'])->name('edit');
            Route::put('/{transaction}', [TransactionController::class, 'update'])->name('update');
        });
        
        Route::delete('/{transaction}', [TransactionController::class, 'destroy'])
            ->name('destroy')
            ->middleware('can:delete,transaction');
        
        // ===== MANAGER ONLY ROUTES =====
        Route::get('/pending/approvals', [TransactionController::class, 'pendingApprovals'])
            ->name('pending.approvals')
            ->middleware('can:viewPendingApprovals,' . Transaction::class);
        
        // Approve action - PATCH method
        Route::patch('/{transaction}/approve', [TransactionController::class, 'approve'])
            ->name('approve')
            ->middleware('can:approve,transaction');
        
        // Reject action - PATCH method
        Route::patch('/{transaction}/reject', [TransactionController::class, 'reject'])
            ->name('reject')
            ->middleware('can:reject,transaction');
        
        // Verify action - PATCH method
        Route::patch('/{transaction}/verify', [TransactionController::class, 'verify'])
            ->name('verify')
            ->middleware('can:verify,transaction');
        
        // Stock update actions - PATCH method
        Route::patch('/{transaction}/ship', [TransactionController::class, 'ship'])
            ->name('ship')
            ->middleware('can:updateStock,transaction');
        
        Route::patch('/{transaction}/complete', [TransactionController::class, 'complete'])
            ->name('complete')
            ->middleware('can:updateStock,transaction');
        
        // ===== STAFF HISTORY =====
        Route::get('/my/history', [TransactionController::class, 'history'])
            ->name('history')
            ->middleware('can:viewHistory,' . Transaction::class);
        
        // ===== REPORTS (Admin & Manager) =====
        Route::get('/reports', [TransactionController::class, 'reports'])
            ->name('reports')
            ->middleware('can:viewReports,' . Transaction::class);
        
        // ===== EXPORT (Admin & Manager) =====
        Route::get('/export', [TransactionController::class, 'export'])
            ->name('export')
            ->middleware('can:export,' . Transaction::class);
    });

   // ========================================
    // RESTOCK ORDERS (Manager & Supplier)
    // ========================================
    Route::prefix('restocks')->name('restocks.')->group(function () {
        // ===== CREATE ROUTES (harus diletakkan SEBELUM show route) =====
        Route::middleware('can:create,' . RestockOrder::class)->group(function () {
            Route::get('/create', [RestockOrderController::class, 'create'])->name('create');
            Route::post('/', [RestockOrderController::class, 'store'])->name('store');
        });
        
        // ===== ACTION ROUTES (harus diletakkan SEBELUM show route) =====
        // Receive - Manager
        Route::post('/{restock}/receive', [RestockOrderController::class, 'receive'])
            ->name('receive')
            //->middleware('can:receive,restock');
            ->middleware('auth');
        
        // Supplier actions - PERBAIKAN: HAPUS '/restocks/' di depan
        Route::post('/{restock}/confirm', [RestockOrderController::class, 'confirm'])
            ->name('confirm')  // ← PERBAIKAN: hanya 'confirm', bukan 'restocks.confirm'
            ->middleware('auth');

        Route::post('/{restock}/deliver', [RestockOrderController::class, 'deliver'])
            ->name('deliver')  // ← PERBAIKAN
            ->middleware('auth');

        Route::post('/{restock}/cancel', [RestockOrderController::class, 'cancel'])
            ->name('cancel')  // ← PERBAIKAN
            ->middleware('auth');
        
        // ===== CRUD ROUTES =====
        // Index
        Route::get('/', [RestockOrderController::class, 'index'])
            ->name('index')
            ->middleware('can:viewAny,' . RestockOrder::class);
        
        // Edit/Update - Manager
        Route::middleware('can:update,restock')->group(function () {
            Route::get('/{restock}/edit', [RestockOrderController::class, 'edit'])->name('edit');
            Route::put('/{restock}', [RestockOrderController::class, 'update'])->name('update');
        });
        
        // Delete
        Route::delete('/{restock}', [RestockOrderController::class, 'destroy'])
            ->name('destroy')
            ->middleware('can:delete,restock');
        
        // ===== SHOW ROUTE (harus diletakkan TERAKHIR) =====
        Route::get('/{restock}', [RestockOrderController::class, 'show'])
            ->name('show')
            ->middleware('can:view,restock');
    });

    // ===== REPORTS (Admin & Manager) =====
    Route::prefix('reports')->name('reports.')->middleware('auth')->group(function () {
        // Halaman utama reports (mungkin dashboard reports)
        Route::get('/', [TransactionController::class, 'reports'])
            ->name('index')
            ->middleware('can:viewReports,' . Transaction::class);
        
        // Inventory report
        Route::get('/inventory', [ReportController::class, 'inventory'])
            ->name('inventory')
            ->middleware('can:viewInventory,' . User::class);
        
        // Low stock report
        Route::get('/low-stock', [ReportController::class, 'lowStock'])
            ->name('low-stock')
            ->middleware('can:viewLowStock,' . User::class);
        
        // Transactions report
        Route::get('/transactions', [ReportController::class, 'transactions'])
            ->name('transactions')
            ->middleware('can:viewTransactions,' . User::class);
    
    });

    // ========================================
    // USERS (Admin only)
    // ========================================
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        
        // Custom actions
        Route::patch('/{user}/status', [UserController::class, 'updateStatus'])
            ->name('updateStatus');
        Route::patch('/{user}/approve', [UserController::class, 'approve'])
            ->name('approve');
        
        Route::delete('/{user}', [UserController::class, 'destroy'])
            ->name('destroy');
    });

    // ========================================
    // STOCK MOVEMENTS (Admin & Manager - View Only)
    // ========================================
    Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
        Route::get('/', [StockMovementController::class, 'index'])->name('index');
        Route::get('/{movement}', [StockMovementController::class, 'show'])->name('show');
        Route::get('/product/{product}', [StockMovementController::class, 'productHistory'])->name('product-history');
        Route::get('/reports', [StockMovementController::class, 'reports'])->name('reports');
        Route::get('/export', [StockMovementController::class, 'export'])->name('export');
    });
});

// ============================================================
// AUTH ROUTES (Login, Register, Password Reset)
// ============================================================
require __DIR__.'/auth.php';