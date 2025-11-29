<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RestockOrder;
use App\Models\Supplier;
use Illuminate\Support\Str;

class RestockOrderSeeder extends Seeder
{
    public function run(): void
    {
        if (Supplier::count() == 0) {
            $this->command->warn("⚠️ Supplier data not found. Run SupplierSeeder first.");
            return;
        }

        $suppliers = Supplier::all();

        $statuses = ['Pending','Confirmed','In Transit','Received'];

        foreach (range(1, 10) as $i) {
            $supplier = $suppliers->random();

            RestockOrder::create([
                'po_number' => 'PO-' . strtoupper(Str::random(8)),
                'supplier_id' => $supplier->id,
                'order_date' => now()->subDays(rand(5, 30)),
                'expected_delivery_date' => now()->addDays(rand(2, 14)),
                'notes' => rand(0, 1) ? 'Urgent restock for fast-moving products.' : null,
                'status' => $statuses[array_rand($statuses)],
            ]);
        }
    }
}
