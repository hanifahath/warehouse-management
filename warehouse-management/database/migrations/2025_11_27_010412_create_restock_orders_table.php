<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restock_orders', function (Blueprint $table) {
            $table->id();

            $table->string('po_number')->unique();

            $table->foreignId('supplier_id')->constrained('users')->cascadeOnDelete(); // Constrained ke users.id
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();

            // Penerimaan Order (BARU DITAMBAHKAN)
            $table->foreignId('received_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('received_at')->nullable();

            $table->enum('status', [
                'Pending',
                'Confirmed by Supplier',
                'In Transit',
                'Received'
            ])->default('Pending');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restock_orders');
    }
};