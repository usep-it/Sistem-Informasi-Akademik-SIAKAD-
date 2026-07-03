<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateJamToRangeInJadwalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jadwals', function (Blueprint $table) {
            // Hapus kolom 'jam' yang lama jika sudah ada
            if (Schema::hasColumn('jadwals', 'jam')) {
                $table->dropColumn('jam');
            }

            // Tambahkan kolom baru setelah 'hari_id'
            $table->time('jam_mulai')->after('hari_id');
            $table->time('jam_selesai')->after('jam_mulai');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jadwals', function (Blueprint $table) {
            // Kembalikan kolom 'jam' dan hapus kolom baru
            $table->string('jam')->nullable(); // atau time('jam')
            $table->dropColumn('jam_mulai');
            $table->dropColumn('jam_selesai');
        });
    }
}