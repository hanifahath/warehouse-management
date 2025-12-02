<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Category;
use App\Models\RestockOrder;
use App\Models\User;
use App\Policies\TransactionPolicy;
use App\Policies\ProductPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\RestockPolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Transaction::class => TransactionPolicy::class,
        Product::class => ProductPolicy::class,
        Category::class => CategoryPolicy::class,
        RestockOrder::class => RestockPolicy::class,
        User::class => UserPolicy::class,
    ];
    
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
