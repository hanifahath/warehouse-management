<?php

namespace Database\Seeders;

use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\RestockOrder;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StockMovementSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🎬 Creating stock movements for demo...');
        
        // Get manager untuk performed_by
        $manager = User::where('email', 'manager@inventory.test')->first();
        
        // 1. Create stock movements from RECEIVED restock orders
        $receivedOrders = RestockOrder::where('status', 'Received')->get();
        
        foreach ($receivedOrders as $order) {
            foreach ($order->items as $item) {
                $product = $item->product;
                
                // Simulate stock increase from restock
                $beforeQty = $product->current_stock - $item->quantity; // Stock sebelum restock
                $afterQty = $product->current_stock; // Stock setelah restock
                
                StockMovement::create([
                    'product_id' => $product->id,
                    'change' => $item->quantity,
                    'source_type' => 'App\Models\RestockOrder',
                    'source_id' => $order->id,
                    'before_qty' => $beforeQty,
                    'after_qty' => $afterQty,
                    'performed_by' => $manager->id,
                    'created_at' => $order->received_at ?? $order->created_at,
                ]);
                
                $this->command->info("✅ Stock IN: {$product->name} +{$item->quantity} (Restock: {$order->po_number})");
            }
        }
        
        // 2. Create stock movements from COMPLETED/SHIPPED outgoing transactions
        $outgoingTransactions = Transaction::where('type', 'outgoing')
            ->whereIn('status', ['completed', 'shipped'])
            ->get();
        
        foreach ($outgoingTransactions as $transaction) {
            foreach ($transaction->items as $item) {
                $product = $item->product;
                
                // Simulate stock decrease from sales
                $beforeQty = $product->current_stock + $item->quantity; // Stock sebelum keluar
                $afterQty = $product->current_stock; // Stock setelah keluar
                
                StockMovement::create([
                    'product_id' => $product->id,
                    'change' => -$item->quantity,
                    'source_type' => 'App\Models\Transaction',
                    'source_id' => $transaction->id,
                    'before_qty' => $beforeQty,
                    'after_qty' => $afterQty,
                    'performed_by' => $transaction->creator->id ?? $manager->id,
                    'created_at' => $transaction->completed_at ?? $transaction->created_at,
                ]);
                
                $this->command->info("✅ Stock OUT: {$product->name} -{$item->quantity} (Transaction: {$transaction->transaction_number})");
            }
        }
        
        // 3. Create stock movements from VERIFIED incoming transactions
        $incomingTransactions = Transaction::where('type', 'incoming')
            ->where('status', 'verified')
            ->get();
        
        foreach ($incomingTransactions as $transaction) {
            foreach ($transaction->items as $item) {
                $product = $item->product;
                
                // Simulate stock increase from incoming
                $beforeQty = $product->current_stock - $item->quantity;
                $afterQty = $product->current_stock;
                
                StockMovement::create([
                    'product_id' => $product->id,
                    'change' => $item->quantity,
                    'source_type' => 'App\Models\Transaction',
                    'source_id' => $transaction->id,
                    'before_qty' => $beforeQty,
                    'after_qty' => $afterQty,
                    'performed_by' => $transaction->creator->id ?? $manager->id,
                    'created_at' => $transaction->completed_at ?? $transaction->created_at,
                ]);
                
                $this->command->info("✅ Stock IN: {$product->name} +{$item->quantity} (Transaction: {$transaction->transaction_number})");
            }
        }
        
        $total = StockMovement::count();
        $inCount = StockMovement::where('change', '>', 0)->count();
        $outCount = StockMovement::where('change', '<', 0)->count();
        
        $this->command->info('');
        $this->command->info('🎉 === STOCK MOVEMENTS CREATED ===');
        $this->command->info("Total movements: {$total}");
        $this->command->info("Stock IN: {$inCount}");
        $this->command->info("Stock OUT: {$outCount}");
    }
}