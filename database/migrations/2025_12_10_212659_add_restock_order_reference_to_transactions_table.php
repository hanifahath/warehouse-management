<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Tambah field restock_order_id
            $table->foreignId('restock_order_id')
                ->nullable()
                ->after('customer_name')
                ->constrained('restock_orders')
                ->nullOnDelete();
            
            // Optional: Tambah field untuk track actual vs ordered
            $table->enum('restock_status', ['pending_receipt', 'partial_receipt', 'complete_receipt', 'not_restock'])
                ->default('not_restock')
                ->after('restock_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['restock_order_id']);
            $table->dropColumn(['restock_order_id', 'restock_status']);
        });
    }
};