<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahuns', function (Blueprint $table) {
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Tidak Aktif')->after('semester');
        });
    }

    public function down(): void
    {
        Schema::table('tahuns', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
