<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RestockOrder;
use App\Models\User;
use Illuminate\Support\Str;

class RestockOrderSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = User::where('role', 'Supplier')->get();
        if ($suppliers->isEmpty()) {
            $this->command->warn("⚠️ Tidak ada supplier.");
            return;
        }

        $statuses = ['Pending','Confirmed','In Transit','Received'];

        foreach (range(1, 5) as $i) {
            $supplier = $suppliers->random();

            RestockOrder::firstOrCreate([
                'po_number' => 'PO-' . strtoupper(Str::random(8))
            ], [
                'supplier_id' => $supplier->id,
                'order_date' => now()->subDays(rand(5, 30)),
                'expected_delivery_date' => now()->addDays(rand(2, 14)),
                'notes' => rand(0,1) ? 'Urgent restock for fast-moving products.' : null,
                'status' => $statuses[array_rand($statuses)],
            ]);
        }
    }
}
