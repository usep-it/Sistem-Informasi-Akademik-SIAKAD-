<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan perubahan ke database.
     */
    public function up(): void
    {
        Schema::table('tahuns', function (Blueprint $table) {
            // Tambahkan kolom semester setelah nama
            $table->enum('semester', ['Ganjil', 'Genap'])->after('nama')->nullable();
        });
    }

    /**
     * Kembalikan perubahan jika dibatalkan (rollback).
     */
    public function down(): void
    {
        Schema::table('tahuns', function (Blueprint $table) {
            $table->dropColumn('semester');
        });
    }
};
