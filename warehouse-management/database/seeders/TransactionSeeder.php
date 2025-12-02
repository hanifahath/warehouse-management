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
        $supplier = User::where('role', 'Supplier')->first();
        $staff = User::where('role', 'Staff')->first();

        // Incoming
        Transaction::firstOrCreate([
            'transaction_number' => 'TRX-IN-001'
        ], [
            'type' => 'Incoming',
            'date' => Carbon::now()->subDays(2),
            'supplier_id' => $supplier?->id,
            'notes' => 'Initial stock refill',
            'status' => 'Verified',
            'created_by' => $staff->id,
        ]);

        // Outgoing
        Transaction::firstOrCreate([
            'transaction_number' => 'TRX-OUT-001'
        ], [
            'type' => 'Outgoing',
            'date' => Carbon::now()->subDay(),
            'customer_name' => 'PT Sinar Jaya',
            'notes' => 'Outgoing shipment to customer',
            'status' => 'Approved',
            'created_by' => $staff->id,
        ]);
    }
}
