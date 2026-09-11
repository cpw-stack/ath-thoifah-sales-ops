<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            // 1. Cek jika kolom 'discount' belum ada, maka buat baru
            if (!Schema::hasColumn('customers', 'discount')) {
                $table->decimal('discount', 5, 2)->default(0)->after('credit_limit');
            }

            // 2. Cek jika kolom 'discount_status' belum ada, maka buat baru
            if (!Schema::hasColumn('customers', 'discount_status')) {
                $table->string('discount_status', 20)->default('inactive')->after('discount');
            } else {
                // Jika sudah ada, ubah tipenya menjadi string agar support status "submitted"
                $table->string('discount_status', 20)->default('inactive')->change();
            }
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Hapus kolom jika di-rollback
            if (Schema::hasColumn('customers', 'discount')) {
                $table->dropColumn('discount');
            }
            if (Schema::hasColumn('customers', 'discount_status')) {
                $table->dropColumn('discount_status');
            }
        });
    }
};