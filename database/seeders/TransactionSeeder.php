<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('transaction_items')->truncate();
        DB::table('transactions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Get sample users
        $staffUser = User::where('email', 'staff1@inventory.test')->first();
        $staffUser2 = User::where('email', 'staff2@inventory.test')->first();
        $managerUser = User::where('email', 'manager@inventory.test')->first();
        $supplierUser1 = User::where('email', 'supplier1@inventory.test')->first();
        $supplierUser2 = User::where('email', 'supplier2@inventory.test')->first();
        $supplierUser3 = User::where('email', 'supplier3@inventory.test')->first();

        // Get sample products
        $products = Product::all();

        $transactions = [];

        // ===================== INCOMING TRANSACTIONS (from suppliers) =====================
        $incomingTransactions = [
            [
                'type' => 'incoming',
                'supplier_id' => $supplierUser1->id,
                'customer_name' => null,
                'created_by' => $staffUser->id,
                'status' => 'approved',
                'date' => Carbon::now()->subDays(30),
                'notes' => 'Stock restock from supplier',
                'total_amount' => 42500000,
                'items' => [
                    ['product_id' => $products->where('sku', 'LAP-DELL-001')->first()->id, 'quantity' => 3, 'price_at_transaction' => 12500000],
                    ['product_id' => $products->where('sku', 'MON-LG-001')->first()->id, 'quantity' => 5, 'price_at_transaction' => 6500000],
                ]
            ],
            [
                'type' => 'incoming',
                'supplier_id' => $supplierUser2->id,
                'customer_name' => null,
                'created_by' => $staffUser->id,
                'status' => 'verified',
                'date' => Carbon::now()->subDays(25),
                'notes' => 'Monthly supplier delivery',
                'total_amount' => 28500000,
                'items' => [
                    ['product_id' => $products->where('sku', 'PHONE-IPHONE-001')->first()->id, 'quantity' => 2, 'price_at_transaction' => 18500000],
                ]
            ],
            [
                'type' => 'incoming',
                'supplier_id' => $supplierUser3->id,
                'customer_name' => null,
                'created_by' => $staffUser2->id,
                'status' => 'completed',
                'date' => Carbon::now()->subDays(20),
                'notes' => 'Components shipment',
                'total_amount' => 85000000,
                'items' => [
                    ['product_id' => $products->where('sku', 'CPU-INTEL-001')->first()->id, 'quantity' => 5, 'price_at_transaction' => 9500000],
                    ['product_id' => $products->where('sku', 'RAM-CORSAIR-001')->first()->id, 'quantity' => 10, 'price_at_transaction' => 2800000],
                    ['product_id' => $products->where('sku', 'SSD-SAMSUNG-001')->first()->id, 'quantity' => 15, 'price_at_transaction' => 3200000],
                ]
            ],
            [
                'type' => 'incoming',
                'supplier_id' => $supplierUser1->id,
                'customer_name' => null,
                'created_by' => $staffUser2->id,
                'status' => 'pending',
                'date' => Carbon::now()->subDays(5),
                'notes' => 'New order awaiting approval',
                'total_amount' => 62000000,
                'items' => [
                    ['product_id' => $products->where('sku', 'LAP-MAC-001')->first()->id, 'quantity' => 2, 'price_at_transaction' => 28500000],
                ]
            ],
            [
                'type' => 'incoming',
                'supplier_id' => $supplierUser2->id,
                'customer_name' => null,
                'created_by' => $staffUser->id,
                'status' => 'rejected',
                'date' => Carbon::now()->subDays(15),
                'notes' => 'Order cancelled by manager',
                'rejection_reason' => 'Price negotiation failed',
                'total_amount' => 35000000,
                'items' => [
                    ['product_id' => $products->where('sku', 'DESK-ASUS-001')->first()->id, 'quantity' => 2, 'price_at_transaction' => 17500000],
                ]
            ],
        ];

        // ===================== OUTGOING TRANSACTIONS (to customers) =====================
        $outgoingTransactions = [
            [
                'type' => 'outgoing',
                'supplier_id' => null,
                'customer_name' => 'PT Maju Jaya Teknologi',
                'created_by' => $staffUser->id,
                'status' => 'shipped',
                'date' => Carbon::now()->subDays(18),
                'notes' => 'Corporate order',
                'total_amount' => 125000000,
                'items' => [
                    ['product_id' => $products->where('sku', 'LAP-LENOVO-001')->first()->id, 'quantity' => 4, 'price_at_transaction' => 21500000],
                    ['product_id' => $products->where('sku', 'MON-LG-001')->first()->id, 'quantity' => 6, 'price_at_transaction' => 8500000],
                ]
            ],
            [
                'type' => 'outgoing',
                'supplier_id' => null,
                'customer_name' => 'CV Abadi Komputer',
                'created_by' => $staffUser2->id,
                'status' => 'completed',
                'date' => Carbon::now()->subDays(12),
                'notes' => 'Reseller order',
                'total_amount' => 45500000,
                'items' => [
                    ['product_id' => $products->where('sku', 'PHONE-SAMSUNG-001')->first()->id, 'quantity' => 2, 'price_at_transaction' => 21500000],
                    ['product_id' => $products->where('sku', 'CABLE-USB-001')->first()->id, 'quantity' => 10, 'price_at_transaction' => 120000],
                    ['product_id' => $products->where('sku', 'CABLE-HDMI-001')->first()->id, 'quantity' => 15, 'price_at_transaction' => 150000],
                ]
            ],
            [
                'type' => 'outgoing',
                'supplier_id' => null,
                'customer_name' => 'Toko Elektronik Sinar',
                'created_by' => $staffUser->id,
                'status' => 'pending',
                'date' => Carbon::now()->subDays(3),
                'notes' => 'Waiting for manager approval',
                'total_amount' => 28500000,
                'items' => [
                    ['product_id' => $products->where('sku', 'GPU-NVIDIA-001')->first()->id, 'quantity' => 1, 'price_at_transaction' => 22500000],
                    ['product_id' => $products->where('sku', 'RAM-CORSAIR-001')->first()->id, 'quantity' => 2, 'price_at_transaction' => 3500000],
                ]
            ],
            [
                'type' => 'outgoing',
                'supplier_id' => null,
                'customer_name' => 'Individual Customer - Bapak Ahmad',
                'created_by' => $staffUser2->id,
                'status' => 'approved',
                'date' => Carbon::now()->subDays(1),
                'notes' => 'Walk-in customer',
                'total_amount' => 6200000,
                'items' => [
                    ['product_id' => $products->where('sku', 'HEADPHONE-SONY-001')->first()->id, 'quantity' => 1, 'price_at_transaction' => 6200000],
                ]
            ],
            [
                'type' => 'outgoing',
                'supplier_id' => null,
                'customer_name' => 'Universitas Teknologi Indonesia',
                'created_by' => $staffUser->id,
                'status' => 'rejected',
                'date' => Carbon::now()->subDays(8),
                'notes' => 'Budget approval failed',
                'rejection_reason' => 'Exceeded department budget',
                'total_amount' => 55000000,
                'items' => [
                    ['product_id' => $products->where('sku', 'PRINTER-HP-001')->first()->id, 'quantity' => 10, 'price_at_transaction' => 5500000],
                ]
            ],
        ];

        // Combine all transactions
        $allTransactions = array_merge($incomingTransactions, $outgoingTransactions);

        $createdCount = 0;
        $totalItems = 0;

        foreach ($allTransactions as $index => $transactionData) {
            // Generate transaction number
            $transactionNumber = Transaction::generateTransactionNumber($transactionData['type']);
            
            // Create transaction
            $transaction = Transaction::create([
                'transaction_number' => $transactionNumber,
                'type' => $transactionData['type'],
                'supplier_id' => $transactionData['supplier_id'],
                'customer_name' => $transactionData['customer_name'],
                'created_by' => $transactionData['created_by'],
                'approved_by' => in_array($transactionData['status'], ['approved', 'rejected', 'verified', 'completed', 'shipped']) 
                    ? $managerUser->id 
                    : null,
                'status' => $transactionData['status'],
                'date' => $transactionData['date'],
                'notes' => $transactionData['notes'],
                'rejection_reason' => $transactionData['rejection_reason'] ?? null,
                'approved_at' => in_array($transactionData['status'], ['approved', 'rejected', 'verified', 'completed', 'shipped'])
                    ? Carbon::parse($transactionData['date'])->addHours(rand(1, 24))
                    : null,
                'completed_at' => in_array($transactionData['status'], ['completed', 'verified', 'shipped'])
                    ? Carbon::parse($transactionData['date'])->addHours(rand(24, 72))
                    : null,
                'shipped_at' => $transactionData['status'] === 'shipped'
                    ? Carbon::parse($transactionData['date'])->addHours(rand(48, 96))
                    : null,
                'total_amount' => $transactionData['total_amount'],
            ]);

            // Create transaction items
            foreach ($transactionData['items'] as $itemData) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'price_at_transaction' => $itemData['price_at_transaction'],
                ]);

                // Update product stock based on transaction type
                $product = Product::find($itemData['product_id']);
                if ($transactionData['type'] === 'incoming' && in_array($transactionData['status'], ['verified', 'completed'])) {
                    // Increase stock for incoming verified/completed
                    $product->increaseStock($itemData['quantity']);
                } elseif ($transactionData['type'] === 'outgoing' && in_array($transactionData['status'], ['completed', 'shipped'])) {
                    // Decrease stock for outgoing completed/shipped
                    $product->decreaseStock($itemData['quantity']);
                }

                $totalItems++;
            }

            $createdCount++;
            $this->command->info("Created transaction: {$transactionNumber} ({$transactionData['status']})");
        }

        // Generate statistics
        $incomingCount = Transaction::incoming()->count();
        $outgoingCount = Transaction::outgoing()->count();
        $pendingCount = Transaction::pending()->count();
        $approvedCount = Transaction::approved()->count();
        $rejectedCount = Transaction::rejected()->count();
        $completedCount = Transaction::completed()->count();
        $shippedCount = Transaction::where('status', 'shipped')->count();
        $verifiedCount = Transaction::where('status', 'verified')->count();
        $totalTransactionValue = Transaction::sum('total_amount');

        $this->command->info('');
        $this->command->info('=== TRANSACTION SEEDER SUMMARY ===');
        $this->command->info("Total transactions created: {$createdCount}");
        $this->command->info("Total items created: {$totalItems}");
        $this->command->info('');
        $this->command->info('=== BY TYPE ===');
        $this->command->info("Incoming transactions: {$incomingCount}");
        $this->command->info("Outgoing transactions: {$outgoingCount}");
        $this->command->info('');
        $this->command->info('=== BY STATUS ===');
        $this->command->info("Pending: {$pendingCount}");
        $this->command->info("Approved: {$approvedCount}");
        $this->command->info("Rejected: {$rejectedCount}");
        $this->command->info("Verified: {$verifiedCount}");
        $this->command->info("Completed: {$completedCount}");
        $this->command->info("Shipped: {$shippedCount}");
        $this->command->info('');
        $this->command->info('=== FINANCIAL ===');
        $this->command->info("Total transaction value: Rp " . number_format($totalTransactionValue, 0, ',', '.'));

        // Show sample transactions
        $this->command->info('');
        $this->command->info('=== SAMPLE TRANSACTIONS ===');
        $sampleTransactions = Transaction::with(['creator', 'supplier', 'items.product'])
            ->inRandomOrder()
            ->limit(3)
            ->get();

        foreach ($sampleTransactions as $transaction) {
            $type = $transaction->is_incoming ? '📥 Incoming' : '📤 Outgoing';
            $statusColor = $transaction->status_color;
            
            $this->command->info("{$type} - {$transaction->transaction_number}");
            $this->command->info("  Status: [{$transaction->status}]");
            $this->command->info("  Date: {$transaction->date->format('d M Y')}");
            $this->command->info("  Created by: {$transaction->creator->name}");
            if ($transaction->supplier) {
                $this->command->info("  Supplier: {$transaction->supplier->name}");
            }
            if ($transaction->customer_name) {
                $this->command->info("  Customer: {$transaction->customer_name}");
            }
            $this->command->info("  Total: Rp " . number_format($transaction->total_amount, 0, ',', '.'));
            $this->command->info("  Items: {$transaction->items->count()} products");
            $this->command->info('');
        }
    }
}