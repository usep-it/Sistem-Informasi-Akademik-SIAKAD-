<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('graduation_settings', function (Blueprint $table) {
            $table->id();
            $table->datetime('waktu_buka')->comment('Waktu pembukaan pengumuman kelulusan');
            $table->datetime('waktu_tutup')->comment('Waktu penutupan pengumuman kelulusan');
            $table->string('status')->default('active')->comment('Status: active atau inactive');
            $table->text('keterangan')->nullable()->comment('Keterangan tambahan');
            $table->timestamps();
        });

        // Insert default data
        \DB::table('graduation_settings')->insert([
            'waktu_buka' => '2026-05-01 00:00:00',
            'waktu_tutup' => '2026-06-30 23:59:59',
            'status' => 'active',
            'keterangan' => 'Pengumuman Kelulusan 2025/2026',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('graduation_settings');
    }
};
