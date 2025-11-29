<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
    Transaction::class => TransactionPolicy::class,
    Product::class => ProductPolicy::class,
    Category::class => CategoryPolicy::class,
    RestockOrder::class => RestockOrderPolicy::class,
    User::class => UserPolicy::class,
    ];
    
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
