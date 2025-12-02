<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionItem;

class TransactionItemSeeder extends Seeder
{
    public function run(): void
    {
        TransactionItem::firstOrCreate([
            'transaction_id' => 1,
            'product_id' => 1,
        ], [
            'quantity' => 10,
        ]);

        TransactionItem::firstOrCreate([
            'transaction_id' => 2,
            'product_id' => 2,
        ], [
            'quantity' => 5,
        ]);
    }
}
