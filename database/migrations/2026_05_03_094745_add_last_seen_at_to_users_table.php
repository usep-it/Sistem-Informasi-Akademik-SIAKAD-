<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastSeenAtToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Tambahkan kolom status (Dibutuhkan oleh LoginController & SiswaController)
            // Kita beri default 'Aktif' agar semua user lama bisa login
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status', 20)->default('Aktif')->after('role');
            }

            // 2. Tambahkan kolom last_seen_at (Untuk Statistik Real-time)
            if (!Schema::hasColumn('users', 'last_seen_at')) {
                $table->timestamp('last_seen_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'last_seen_at']);
        });
    }
}