<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();   // auto-generated
            $table->enum('type', ['Incoming', 'Outgoing']);   // barang masuk/keluar
            $table->date('date');                             // tanggal penerimaan/pengiriman
            $table->foreignId('supplier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('customer_name')->nullable();      // untuk outgoing
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('Pending');     // Pending, Verified, Completed, Approved, Shipped
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};