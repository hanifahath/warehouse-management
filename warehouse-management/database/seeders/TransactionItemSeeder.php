<?php

namespace Database\Seeders;

use App\Models\TransactionItem;
use Illuminate\Database\Seeder;

class TransactionItemSeeder extends Seeder
{
    public function run(): void
    {
        // Item untuk Transaksi ID 1 (Incoming)
        TransactionItem::create([
            'transaction_id' => 1,
            'product_id' => 1, // Laptop
            // HAPUS INI: 'warehouse_id' => 1,
            'quantity' => 10,
            'price_at_transaction' => 8000000, // Gunakan harga beli (purchase price)
        ]);

        // Item untuk Transaksi ID 2 (Outgoing)
        TransactionItem::create([
            'transaction_id' => 2,
            'product_id' => 2, // Office Chair
            // HAPUS INI: 'warehouse_id' => 1,
            'quantity' => 5,
            'price_at_transaction' => 450000, // Gunakan harga jual (selling price)
        ]);
    }
}