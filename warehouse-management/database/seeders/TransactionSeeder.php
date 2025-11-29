<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada supplier & staff/manager
        $supplier = User::firstOrCreate(
            ['email' => 'supplier@demo.com'],
            [
                'name' => 'Demo Supplier',
                'password' => bcrypt('password'),
                'role' => 'Supplier',
                'is_approved' => true,
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@demo.com'],
            [
                'name' => 'Demo Staff',
                'password' => bcrypt('password'),
                'role' => 'Staff',
                'is_approved' => true,
            ]
        );

        // --- Create Incoming Transaction (barang masuk) ---
        Transaction::create([
            'transaction_number' => 'TRX-IN-' . now()->timestamp,
            'type' => 'Incoming',
            'date' => Carbon::now()->subDays(2),
            'supplier_id' => $supplier->id,
            'notes' => 'Initial stock refill',
            'status' => 'Verified',
            'created_by' => $staff->id,
        ]);

        // --- Create Outgoing Transaction (barang keluar) ---
        Transaction::create([
            'transaction_number' => 'TRX-OUT-' . (now()->timestamp + 1),
            'type' => 'Outgoing',
            'date' => Carbon::now()->subDay(),
            'customer_name' => 'PT Sinar Jaya',
            'notes' => 'Outgoing shipment to customer',
            'status' => 'Approved',
            'created_by' => $staff->id,
        ]);
    }
}
