<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockMovement;

class StockMovementSeeder extends Seeder
{
    public function run(): void
    {
        // Audit trail untuk transaksi Incoming (ID 1)
        StockMovement::create([
            'product_id' => 1,          // Laptop
            'type' => 'incoming',
            'quantity' => 10,
            'reference_type' => 'Transaction',
            'reference_id' => 1,
            'user_id' => 3,             // Staff Gudang
        ]);

        // Audit trail untuk transaksi Outgoing (ID 2)
        StockMovement::create([
            'product_id' => 2,          // Office Chair
            'type' => 'outgoing',
            'quantity' => 5,
            'reference_type' => 'Transaction',
            'reference_id' => 2,
            'user_id' => 3,             // Staff Gudang
        ]);

        // Audit trail untuk Restock Order (ID 1)
        StockMovement::create([
            'product_id' => 1,          // Laptop
            'type' => 'incoming',
            'quantity' => 5,
            'reference_type' => 'RestockOrder',
            'reference_id' => 1,
            'user_id' => 2,             // Manager User
        ]);
    }
}