<?php

namespace Database\Seeders;

use App\Models\RestockOrder;
use Illuminate\Database\Seeder;

class RestockOrderSeeder extends Seeder
{
    public function run(): void
    {
        RestockOrder::create([
            'po_number' => 'PO-' . str_pad(RestockOrder::count()+1, 4, '0', STR_PAD_LEFT),
            'supplier_id' => 1,
            'created_by' => 1,
            'order_date' => now(),
            'expected_delivery_date' => now()->addDays(5),
            'status' => 'Pending',
            'notes' => 'Initial restock order',
        ]);
    }
}
