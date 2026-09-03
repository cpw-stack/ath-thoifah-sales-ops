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
        // 1. Cek dulu kalau kolom is_approved belum ada, baru tambahkan
        if (!Schema::hasColumn('customer_stock_discounts', 'is_approved')) {
            Schema::table('customer_stock_discounts', function (Blueprint $table) {
                $table->boolean('is_approved')->default(false)->after('is_active');
            });
        }

        // 2. Cek dulu kalau kolom 'value' ada dan 'discount_value' belum ada, baru rename
        if (Schema::hasColumn('customer_stock_discounts', 'value') && !Schema::hasColumn('customer_stock_discounts', 'discount_value')) {
            Schema::table('customer_stock_discounts', function (Blueprint $table) {
                $table->renameColumn('value', 'discount_value');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customer_stock_discounts', 'discount_value')) {
            Schema::table('customer_stock_discounts', function (Blueprint $table) {
                $table->renameColumn('discount_value', 'value');
            });
        }
        
        if (Schema::hasColumn('customer_stock_discounts', 'is_approved')) {
            Schema::table('customer_stock_discounts', function (Blueprint $table) {
                $table->dropColumn('is_approved');
            });
        }
    }
};
