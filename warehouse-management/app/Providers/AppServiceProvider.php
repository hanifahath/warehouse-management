<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // Import Facade URL
use Illuminate\Support\Facades\Schema; // Import Facade Schema (Opsional, tapi sering digunakan)
use Illuminate\Routing\Router; // Import Router untuk mendaftarkan middleware

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // Singleton atau bind sesuai kebutuhan
        $this->app->singleton(\App\Services\TransactionService::class, function ($app) {
            return new \App\Services\TransactionService();
        });

        $this->app->singleton(\App\Services\InventoryService::class, function ($app) {
            return new \App\Services\InventoryService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Router $router): void // Inject Router di sini
    {
        // 1. Set default string length for MySQL (mengatasi error index Laravel lama)
        Schema::defaultStringLength(191);

        // 2. [SOLUSI ERROR BINDING ROLE] Daftarkan Middleware 'role' secara eksplisit melalui Router
        // Ini adalah cara yang lebih pasti daripada hanya menggunakan Kernel.php, dan dapat mengatasi masalah cache/binding.
        $router->aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);
        
        // Opsional: Untuk force HTTPS jika diperlukan
        // if (env('APP_ENV') === 'production') {
        //     URL::forceScheme('https');
        // }
    }
}