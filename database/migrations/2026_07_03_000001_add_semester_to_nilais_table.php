<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nilais', function (Blueprint $table) {
            // Tambah kolom semester jika belum ada
            if (!Schema::hasColumn('nilais', 'semester')) {
                $table->enum('semester', ['Genap', 'Ganjil'])->nullable()->after('tahun_id');
            }
        });

        // Update nilai existing dengan semester berdasarkan tahun_id (MariaDB syntax)
        DB::statement("
            UPDATE nilais n
            INNER JOIN tahuns t ON n.tahun_id = t.id
            SET n.semester = t.semester
            WHERE n.semester IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilais', function (Blueprint $table) {
            $table->dropColumn('semester');
        });
    }
};
