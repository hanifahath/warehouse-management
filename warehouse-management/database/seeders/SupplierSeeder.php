<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::create([
            'name' => 'PT Sumber Elektronik',
            'email' => 'supplier1@example.com',
            'phone' => '08123456789',
        ]);

        Supplier::create([
            'name' => 'UD Maju Jaya',
            'email' => 'supplier2@example.com',
            'phone' => '08987654321',
        ]);
    }
}
