<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            RestockOrderSeeder::class,
            RestockItemSeeder::class,
            TransactionSeeder::class,
            TransactionItemSeeder::class,
            StockMovementSeeder::class, 
        ]);
    }
}