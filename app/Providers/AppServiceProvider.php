<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // Import Facade URL
use Illuminate\Support\Facades\Schema; // Import Facade Schema (Opsional, tapi sering digunakan)
use Illuminate\Routing\Router; // Import Router untuk mendaftarkan middleware
use Illuminate\Database\Eloquent\Relations\Relation;
// PERBAIKAN 1: Hapus import yang tidak perlu atau perbaiki typo
use App\Services\TransactionService;
use App\Services\StockMovementService; // PERBAIKAN 2: Perbaiki typo 'Seervices' menjadi 'Services'
// use App\Seervices\StockMovementService; // BARIS TYPO ASLI

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // PERBAIKAN 3: Menyuntikkan StockMovementService ke TransactionService
        $this->app->singleton(TransactionService::class, function ($app) {
            // Kita secara eksplisit memberitahu Laravel untuk membuat (make) instance StockMovementService
            // dan meneruskannya sebagai argumen.
            return new TransactionService(
                $app->make(StockMovementService::class)
            );
        $this->app->bind(ProductService::class, function ($app) {
            return new ProductService();
        });
        $this->app->bind(CategoryService::class, function ($app) {
            return new CategoryService();
        });
        });

        // Binding InventoryService, jika ia tidak memiliki dependensi di constructor, 
        // ini sudah benar, tetapi lebih bersih jika menggunakan bind/singleton tanpa closure
        $this->app->singleton(\App\Services\InventoryService::class); 
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Router $router): void // Inject Router di sini
    {
        // 1. Set default string length for MySQL (mengatasi error index Laravel lama)
        Schema::defaultStringLength(191);

        // 2. [SOLUSI ERROR BINDING ROLE] Daftarkan Middleware 'role' secara eksplisit melalui Router
        $router->aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);

        // Morph map for legacy polymorphic types (fixes "Class 'restock' not found")
        Relation::morphMap([
            'restock' => \App\Models\RestockOrder::class,
            'transaction_in' => \App\Models\Transaction::class,
            'transaction_out' => \App\Models\Transaction::class,
        ], false);
        
        // Opsional: Untuk force HTTPS jika diperlukan
        // if (env('APP_ENV') === 'production') {
        //     URL::forceScheme('https');
        // }
    }
}