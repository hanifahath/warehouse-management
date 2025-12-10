<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restock_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('restock_orders', 'manager_id')) {
                $table->unsignedBigInteger('manager_id')->nullable()->after('supplier_id');
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
        });

        // ✅ Set default manager_id untuk data yang belum punya
        $firstManager = DB::table('users')
            ->where('role', 'manager')
            ->first();

        if ($firstManager) {
            DB::table('restock_orders')
                ->whereNull('manager_id')
                ->update(['manager_id' => $firstManager->id]);
        } else {
            $firstAdmin = DB::table('users')
                ->where('role', 'admin')
                ->first();
            
            if ($firstAdmin) {
                DB::table('restock_orders')
                    ->whereNull('manager_id')
                    ->update(['manager_id' => $firstAdmin->id]);
            }
        }

        // ✅ Tambah foreign key jika belum ada
        $foreignKeys = collect(DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'restock_orders' 
            AND CONSTRAINT_NAME = 'restock_orders_manager_id_foreign'
        "));

        if ($foreignKeys->isEmpty()) {
            Schema::table('restock_orders', function (Blueprint $table) {
                $table->foreign('manager_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('restock_orders', function (Blueprint $table) {
            if (Schema::hasColumn('restock_orders', 'manager_id')) {
                $table->dropForeign(['manager_id']);
                $table->dropColumn('manager_id');
            }
            
            $columns = ['confirmed_at', 'shipped_at', 'cancelled_at', 'cancellation_reason'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('restock_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};