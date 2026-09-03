<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mitra_stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mitra_stock_id')->constrained()->onDelete('cascade');
            $table->foreignId('visit_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['in', 'out', 'retur', 'adjustment']);
            $table->enum('status', ['konsinyasi', 'lunas', 'piutang']);
            $table->integer('qty');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('employees')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitra_stock_transactions');
    }
};
