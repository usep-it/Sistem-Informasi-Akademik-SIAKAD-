<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIpAddressToUsersTable extends Migration
{
    /**
     * Jalankan migrasi untuk menambahkan kolom IP Address.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom ip_address menggunakan panjang 45 untuk mendukung format IPv6
            if (!Schema::hasColumn('users', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('last_seen_at');
            }
        });
    }

    /**
     * Kembalikan perubahan jika migrasi di-rollback.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ip_address');
        });
    }
}