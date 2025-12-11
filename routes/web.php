<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestockOrderController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StockMovementController;
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

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Supplier pending page - accessible by unapproved suppliers
Route::get('/pending', function () {
    $user = auth()->user();
    
    if (!$user) {
        return redirect()->route('login');
    }
    
    if ($user->role !== 'supplier') {
        return redirect()->route('dashboard');
    }
    
    if ($user->is_approved) {
        return redirect()->route('supplier.restocks.index');
    }
    
    return view('supplier.pending');
})->name('supplier.pending')->middleware('auth');

// ============================================================
// AUTHENTICATED ROUTES
// ============================================================

Route::middleware(['auth', 'verified', 'supplier.approved'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Supplier specific routes
    Route::get('/supplier/restocks', [RestockOrderController::class, 'supplierIndex'])
        ->name('supplier.restocks.index');

    // Products
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])
            ->name('index')
            ->middleware('can:viewAny,' . Product::class);
        
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
        
        Route::delete('/{product}', [ProductController::class, 'destroy'])
            ->name('destroy')
            ->middleware('can:delete,product');

        Route::get('/{product}', [ProductController::class, 'show'])
            ->name('show')
            ->middleware('can:view,product');
    });

    // Categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::middleware('can:create,' . Category::class)->group(function () {
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
        });

        Route::get('/', [CategoryController::class, 'index'])
            ->name('index')
            ->middleware('can:viewAny,' . Category::class);
        
        Route::get('/{category}', [CategoryController::class, 'show'])
            ->name('show')
            ->middleware('can:view,category');
        
        Route::middleware('can:update,category')->group(function () {
            Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        });
        
        Route::delete('/{category}', [CategoryController::class, 'destroy'])
            ->name('destroy')
            ->middleware('can:delete,category');
    });

    // Transactions
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])
            ->name('index')
            ->middleware('can:viewAny,' . Transaction::class);
        
        Route::get('/{transaction}', [TransactionController::class, 'show'])
            ->name('show')
            ->middleware('can:view,transaction');
        
        // Staff only routes
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
        
        // Manager only routes
        Route::get('/pending/approvals', [TransactionController::class, 'pendingApprovals'])
            ->name('pending.approvals')
            ->middleware('can:viewPendingApprovals,' . Transaction::class);
        
        Route::patch('/{transaction}/approve', [TransactionController::class, 'approve'])
            ->name('approve')
            ->middleware('can:approve,transaction');
        
        Route::patch('/{transaction}/reject', [TransactionController::class, 'reject'])
            ->name('reject')
            ->middleware('can:reject,transaction');
        
        Route::patch('/{transaction}/verify', [TransactionController::class, 'verify'])
            ->name('verify')
            ->middleware('can:verify,transaction');
        
        Route::patch('/{transaction}/ship', [TransactionController::class, 'ship'])
            ->name('ship')
            ->middleware('can:updateStock,transaction');
        
        Route::patch('/{transaction}/complete', [TransactionController::class, 'complete'])
            ->name('complete')
            ->middleware('can:updateStock,transaction');
        
        // Staff history
        Route::get('/my/history', [TransactionController::class, 'history'])
            ->name('history')
            ->middleware('can:viewHistory,' . Transaction::class);
    });

    // Restock Orders
    Route::prefix('restocks')->name('restocks.')->group(function () {
        // Create routes
        Route::middleware('can:create,' . RestockOrder::class)->group(function () {
            Route::get('/create', [RestockOrderController::class, 'create'])->name('create');
            Route::post('/', [RestockOrderController::class, 'store'])->name('store');
        });
        
        // Action routes
        Route::post('/{restock}/receive', [RestockOrderController::class, 'receive'])
            ->name('receive');
        
        Route::post('/{restock}/confirm', [RestockOrderController::class, 'confirm'])
            ->name('confirm');

        Route::post('/{restock}/deliver', [RestockOrderController::class, 'deliver'])
            ->name('deliver');

        Route::post('/{restock}/cancel', [RestockOrderController::class, 'cancel'])
            ->name('cancel');
        
        // CRUD routes
        Route::get('/', [RestockOrderController::class, 'index'])
            ->name('index')
            ->middleware('can:viewAny,' . RestockOrder::class);
        
        Route::middleware('can:update,restock')->group(function () {
            Route::get('/{restock}/edit', [RestockOrderController::class, 'edit'])->name('edit');
            Route::put('/{restock}', [RestockOrderController::class, 'update'])->name('update');
        });
        
        Route::delete('/{restock}', [RestockOrderController::class, 'destroy'])
            ->name('destroy')
            ->middleware('can:delete,restock');
        
        // Show route
        Route::get('/{restock}', [RestockOrderController::class, 'show'])
            ->name('show')
            ->middleware('can:view,restock');
    });

    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        
        Route::patch('/{user}/status', [UserController::class, 'updateStatus'])
            ->name('updateStatus');
        Route::patch('/{user}/approve', [UserController::class, 'approve'])
            ->name('approve');
        
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Stock Movements
    Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
        Route::get('/', [StockMovementController::class, 'index'])->name('index');
        Route::get('/{movement}', [StockMovementController::class, 'show'])->name('show');
        Route::get('/product/{product}', [StockMovementController::class, 'productHistory'])
            ->name('product-history');
    });
});

require __DIR__.'/auth.php';