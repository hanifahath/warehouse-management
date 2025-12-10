<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restock_orders', function (Blueprint $table) {
            // Cek apakah kolom sudah ada sebelum ditambahkan
            if (!Schema::hasColumn('restock_orders', 'manager_id')) {
                $table->foreignId('manager_id')->after('supplier_id')
                    ->constrained('users')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('restock_orders', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('restock_orders', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable()->after('confirmed_at');
            }
            
            if (!Schema::hasColumn('restock_orders', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('received_at');
            }
            
            if (!Schema::hasColumn('restock_orders', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            }
            
            if (!Schema::hasColumn('restock_orders', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->default(0)->after('cancellation_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restock_orders', function (Blueprint $table) {
            if (Schema::hasColumn('restock_orders', 'manager_id')) {
                $table->dropForeign(['manager_id']);
                $table->dropColumn('manager_id');
            }
            
            $columns = ['confirmed_at', 'shipped_at', 'cancelled_at', 'cancellation_reason', 'total_amount'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('restock_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};