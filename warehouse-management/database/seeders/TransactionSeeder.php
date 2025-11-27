<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        Transaction::create([
            'transaction_number' => 'TRX-' . Str::random(8),
            'type' => 'Incoming',
            // HAPUS INI: 'warehouse_id' => 1,
            'supplier_id' => 4, // Gunakan ID Supplier Aktif (misalnya ID 4 dari UserSeeder yang kita buat)
            'created_by' => 3,  // Gunakan ID Staff Gudang (misalnya ID 3)
            'status' => 'Pending',
            'date' => now(),
            'notes' => 'Initial seeded incoming transaction'
        ]);
        
        // Tambahkan satu transaksi Outgoing untuk pengujian
        Transaction::create([
            'transaction_number' => 'TRX-' . Str::random(8),
            'type' => 'Outgoing',
            // HAPUS INI: 'warehouse_id' => 1,
            'customer_name' => 'Budi Retail', // Ganti supplier_id dengan customer_name
            'created_by' => 3,  // Staff Gudang
            'status' => 'Pending',
            'date' => now(),
            'notes' => 'Initial seeded outgoing transaction'
        ]);
    }
}