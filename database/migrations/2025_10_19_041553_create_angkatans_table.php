<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAngkatansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('angkatans', function (Blueprint $table) {
        $table->id();
        $table->string('nama'); // Contoh: "Angkatan 2025"
        $table->year('tahun_lulus');
        $table->integer('jumlah_siswa')->default(0);
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('angkatans');
}

}
