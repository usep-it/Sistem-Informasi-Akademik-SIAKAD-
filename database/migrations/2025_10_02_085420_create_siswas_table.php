<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid'); // UUID unik
            $table->string('nama');
            $table->enum('jk', ['Laki-Laki', 'Perempuan']);
            $table->string('tempat');
            $table->date('ttl'); // TTL harus berupa tanggal
            $table->text('alamat');
            $table->string('nis')->unique();
            $table->string('nisn', 20)->unique();
            $table->string('hp', 15)->nullable();
            $table->unsignedBigInteger('kelas_id')->nullable(); // relasi ke kelases
            $table->timestamps();

            // Foreign key ke tabel kelases
            $table->foreign('kelas_id')
                  ->references('id')
                  ->on('kelases')
                  ->onDelete('set null');
        });
    }

    /**
     * Hapus tabel saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
