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
        Schema::create('nilais', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid', 36);
            $table->integer('pegawai_id');
            $table->integer('jadwal_id');
            $table->integer('jadwal_siswa_id')->index('jadwal_siswa_id');
            $table->string('nilai', 100);
            $table->integer('siswa_id')->nullable();
            $table->integer('tahun_id')->nullable();
            $table->integer('kelas_id')->nullable();
            $table->integer('mapel_id');
            $table->string('jenis', 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nilais');
    }
};
