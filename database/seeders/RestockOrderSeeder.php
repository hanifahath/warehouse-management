<?php

namespace Database\Seeders;

use App\Models\RestockOrder;
use App\Models\RestockItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RestockOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('restock_items')->truncate();
        DB::table('restock_orders')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get sample users
        $users = User::all();
        
        // Cari user berdasarkan role atau ambil random
        $managerUser = $users->where('role', 'Manager')->first() ?? $users->random();
        $opsManager = $users->where('role', 'Operations Manager')->first() ?? $users->random();
        $supplierUser1 = $users->where('role', 'Supplier')->first() ?? $users->random();
        $supplierUser2 = $users->where('role', 'Supplier')->skip(1)->first() ?? $users->random();
        $supplierUser3 = $users->where('role', 'Supplier')->skip(2)->first() ?? $users->random();

        // Get sample products
        $products = Product::all();

        // HARCODE PO NUMBERS - PASTIKAN UNIK!
        $restockOrders = [
            // ===================== PENDING ORDERS =====================
            [
                'po_number' => 'PO-20251210-0001',
                'supplier_id' => $supplierUser1->id,
                'manager_id' => $managerUser->id,
                'order_date' => Carbon::now()->subDays(3),
                'expected_delivery_date' => Carbon::now()->addDays(7),
                'status' => 'Pending', // 7 karakter
                'notes' => 'Urgent restock for low inventory items',
                'total_amount' => 85000000,
                'items' => [
                    ['product_id' => $products->where('sku', 'LAP-DELL-001')->first()->id, 'quantity' => 5],
                    ['product_id' => $products->where('sku', 'MON-LG-001')->first()->id, 'quantity' => 8],
                    ['product_id' => $products->where('sku', 'RAM-CORSAIR-001')->first()->id, 'quantity' => 15],
                ]
            ],
            [
                'po_number' => 'PO-20251210-0002',
                'supplier_id' => $supplierUser2->id,
                'manager_id' => $opsManager->id,
                'order_date' => Carbon::now()->subDays(2),
                'expected_delivery_date' => Carbon::now()->addDays(5),
                'status' => 'Pending',
                'notes' => 'Monthly restock for mobile devices',
                'total_amount' => 37000000,
                'items' => [
                    ['product_id' => $products->where('sku', 'PHONE-IPHONE-001')->first()->id, 'quantity' => 2],
                    ['product_id' => $products->where('sku', 'PHONE-SAMSUNG-001')->first()->id, 'quantity' => 2],
                ]
            ],

            // ===================== CONFIRMED ORDERS =====================
            [
                'po_number' => 'PO-20251209-0001',
                'supplier_id' => $supplierUser3->id,
                'manager_id' => $managerUser->id,
                'order_date' => Carbon::now()->subDays(10),
                'expected_delivery_date' => Carbon::now()->addDays(2),
                'status' => 'Confirmed', // 9 karakter
                'confirmed_at' => Carbon::now()->subDays(8),
                'notes' => 'Confirmed by supplier, preparing shipment',
                'total_amount' => 45000000,
                'items' => [
                    ['product_id' => $products->where('sku', 'CPU-INTEL-001')->first()->id, 'quantity' => 3],
                    ['product_id' => $products->where('sku', 'GPU-NVIDIA-001')->first()->id, 'quantity' => 2],
                ]
            ],
            [
                'po_number' => 'PO-20251209-0002',
                'supplier_id' => $supplierUser1->id,
                'manager_id' => $opsManager->id,
                'order_date' => Carbon::now()->subDays(12),
                'expected_delivery_date' => Carbon::now()->addDays(1),
                'status' => 'Confirmed',
                'confirmed_at' => Carbon::now()->subDays(9),
                'notes' => 'Components for workstation builds',
                'total_amount' => 26500000,
                'items' => [
                    ['product_id' => $products->where('sku', 'SSD-SAMSUNG-001')->first()->id, 'quantity' => 8],
                    ['product_id' => $products->where('sku', 'HDD-WD-001')->first()->id, 'quantity' => 12],
                ]
            ],

            // ===================== IN TRANSIT ORDERS =====================
            [
                'po_number' => 'PO-20251208-0001',
                'supplier_id' => $supplierUser2->id,
                'manager_id' => $managerUser->id,
                'order_date' => Carbon::now()->subDays(15),
                'expected_delivery_date' => Carbon::now()->tomorrow(),
                'status' => 'In Transit', // 9 karakter
                'confirmed_at' => Carbon::now()->subDays(13),
                'shipped_at' => Carbon::now()->subDays(2),
                'notes' => 'Shipped via express delivery',
                'total_amount' => 18500000,
                'items' => [
                    ['product_id' => $products->where('sku', 'HEADPHONE-SONY-001')->first()->id, 'quantity' => 3],
                    ['product_id' => $products->where('sku', 'KEY-LOGI-001')->first()->id, 'quantity' => 6],
                    ['product_id' => $products->where('sku', 'MOUSE-RAZER-001')->first()->id, 'quantity' => 10],
                ]
            ],
            [
                'po_number' => 'PO-20251208-0002',
                'supplier_id' => $supplierUser3->id,
                'manager_id' => $opsManager->id,
                'order_date' => Carbon::now()->subDays(18),
                'expected_delivery_date' => Carbon::now()->today(),
                'status' => 'In Transit',
                'confirmed_at' => Carbon::now()->subDays(15),
                'shipped_at' => Carbon::now()->subDays(3),
                'notes' => 'Network equipment shipment',
                'total_amount' => 4900000,
                'items' => [
                    ['product_id' => $products->where('sku', 'ROUTER-TPLINK-001')->first()->id, 'quantity' => 2],
                    ['product_id' => $products->where('sku', 'CABLE-HDMI-001')->first()->id, 'quantity' => 25],
                    ['product_id' => $products->where('sku', 'CABLE-USB-001')->first()->id, 'quantity' => 30],
                ]
            ],

            // ===================== RECEIVED ORDERS =====================
            [
                'po_number' => 'PO-20251201-0001',
                'supplier_id' => $supplierUser1->id,
                'manager_id' => $managerUser->id,
                'order_date' => Carbon::now()->subDays(25),
                'expected_delivery_date' => Carbon::now()->subDays(18),
                'status' => 'Received', // 8 karakter
                'confirmed_at' => Carbon::now()->subDays(23),
                'shipped_at' => Carbon::now()->subDays(20),
                'received_at' => Carbon::now()->subDays(18),
                'notes' => 'Received and stocked in warehouse',
                'total_amount' => 65000000,
                'items' => [
                    ['product_id' => $products->where('sku', 'LAP-MAC-001')->first()->id, 'quantity' => 3],
                    ['product_id' => $products->where('sku', 'LAP-LENOVO-001')->first()->id, 'quantity' => 4],
                ]
            ],
            [
                'po_number' => 'PO-20251201-0002',
                'supplier_id' => $supplierUser2->id,
                'manager_id' => $opsManager->id,
                'order_date' => Carbon::now()->subDays(30),
                'expected_delivery_date' => Carbon::now()->subDays(22),
                'status' => 'Received',
                'confirmed_at' => Carbon::now()->subDays(28),
                'shipped_at' => Carbon::now()->subDays(25),
                'received_at' => Carbon::now()->subDays(22),
                'notes' => 'Office supplies received',
                'total_amount' => 27500000,
                'items' => [
                    ['product_id' => $products->where('sku', 'PRINTER-HP-001')->first()->id, 'quantity' => 5],
                ]
            ],

            // ===================== CANCELLED ORDERS (GANTI KE 'Canceled' 8 karakter) =====================
            [
                'po_number' => 'PO-20251205-0001',
                'supplier_id' => $supplierUser3->id,
                'manager_id' => $managerUser->id,
                'order_date' => Carbon::now()->subDays(8),
                'expected_delivery_date' => Carbon::now()->addDays(4),
                'status' => 'Cancelled', // Hanya 8 karakter, bukan 'Cancelled' (9 karakter)
                'cancelled_at' => Carbon::now()->subDays(6),
                'cancellation_reason' => 'Supplier cannot fulfill order due to stock shortage',
                'notes' => 'Order cancelled by supplier',
                'total_amount' => 34000000,
                'items' => [
                    ['product_id' => $products->where('sku', 'DESK-ASUS-001')->first()->id, 'quantity' => 2],
                ]
            ],
            [
                'po_number' => 'PO-20251205-0002',
                'supplier_id' => $supplierUser1->id,
                'manager_id' => $opsManager->id,
                'order_date' => Carbon::now()->subDays(5),
                'expected_delivery_date' => Carbon::now()->addDays(3),
                'status' => 'Cancelled', // Hanya 8 karakter
                'cancelled_at' => Carbon::now()->subDays(4),
                'cancellation_reason' => 'Found better price from another supplier',
                'notes' => 'Cancelled for price negotiation',
                'total_amount' => 42000000,
                'items' => [
                    ['product_id' => $products->where('sku', 'DESK-HP-001')->first()->id, 'quantity' => 4],
                ]
            ],
        ];

        $createdCount = 0;
        $totalItems = 0;

        foreach ($restockOrders as $orderData) {
            try {
                // Debug: Tampilkan status yang akan dimasukkan
                $statusLength = strlen($orderData['status']);
                $this->command->info("Status: '{$orderData['status']}' ({$statusLength} characters)");
                
                // Create restock order
                $restockOrder = RestockOrder::create([
                    'po_number' => $orderData['po_number'],
                    'supplier_id' => $orderData['supplier_id'],
                    'manager_id' => $orderData['manager_id'],
                    'order_date' => $orderData['order_date'],
                    'expected_delivery_date' => $orderData['expected_delivery_date'],
                    'status' => $orderData['status'],
                    'notes' => $orderData['notes'],
                    'confirmed_at' => $orderData['confirmed_at'] ?? null,
                    'shipped_at' => $orderData['shipped_at'] ?? null,
                    'received_at' => $orderData['received_at'] ?? null,
                    'cancelled_at' => $orderData['cancelled_at'] ?? null,
                    'cancellation_reason' => $orderData['cancellation_reason'] ?? null,
                    'total_amount' => $orderData['total_amount'],
                    'created_at' => $orderData['order_date'],
                    'updated_at' => $orderData['order_date'],
                ]);

                // Create restock items
                foreach ($orderData['items'] as $itemData) {
                    RestockItem::create([
                        'restock_order_id' => $restockOrder->id,
                        'product_id' => $itemData['product_id'],
                        'quantity' => $itemData['quantity'],
                        'created_at' => $orderData['order_date'],
                        'updated_at' => $orderData['order_date'],
                    ]);

                    // Update product stock if order is received
                    if ($orderData['status'] === 'Received') {
                        $product = Product::find($itemData['product_id']);
                        if ($product) {
                            $product->current_stock += $itemData['quantity'];
                            $product->save();
                        }
                    }

                    $totalItems++;
                }

                $createdCount++;
                $this->command->info("✅ Created restock order: {$orderData['po_number']} ({$orderData['status']})");
                
            } catch (\Exception $e) {
                $this->command->error("❌ Failed to create order {$orderData['po_number']}: " . $e->getMessage());
                // Debug lebih detail
                $this->command->error("Error details: " . $e->getFile() . ":" . $e->getLine());
            }
        }

        if ($createdCount > 0) {
            // Generate statistics
            $pendingCount = RestockOrder::where('status', 'Pending')->count();
            $confirmedCount = RestockOrder::where('status', 'Confirmed')->count();
            $inTransitCount = RestockOrder::where('status', 'In Transit')->count();
            $receivedCount = RestockOrder::where('status', 'Received')->count();
            $cancelledCount = RestockOrder::where('status', 'like', '%Cancel%')->count();
            $totalOrderValue = RestockOrder::sum('total_amount');

            $this->command->info('');
            $this->command->info('🎉 === RESTOCK ORDER SEEDER SUMMARY ===');
            $this->command->info("Total orders created: {$createdCount}");
            $this->command->info("Total items created: {$totalItems}");
            $this->command->info('');
            $this->command->info('📊 === BY STATUS ===');
            $this->command->info("⏳ Pending: {$pendingCount}");
            $this->command->info("✅ Confirmed: {$confirmedCount}");
            $this->command->info("🚚 In Transit: {$inTransitCount}");
            $this->command->info("📦 Received: {$receivedCount}");
            $this->command->info("❌ Cancelled/Canceled: {$cancelledCount}");
            $this->command->info('');
            $this->command->info('💰 === FINANCIAL ===');
            $this->command->info("Total order value: Rp " . number_format($totalOrderValue, 0, ',', '.'));
        } else {
            $this->command->error('❌ No restock orders were created!');
        }
    }
}