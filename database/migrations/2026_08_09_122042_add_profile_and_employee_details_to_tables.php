<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('email');
            $table->string('whatsapp')->nullable()->after('photo');
            $table->text('address')->nullable()->after('whatsapp');
        });

        // Tambah kolom ke tabel employees
        Schema::table('employees', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->after('phone_number');
            $table->enum('gender', ['male', 'female'])->nullable()->after('birth_date');
            $table->string('id_card_number')->nullable()->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['photo', 'whatsapp', 'address']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['birth_date', 'gender', 'id_card_number']);
        });
    }
};