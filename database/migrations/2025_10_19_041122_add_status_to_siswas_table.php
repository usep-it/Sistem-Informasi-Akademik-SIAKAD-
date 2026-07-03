<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToSiswasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('siswas', function (Blueprint $table) {
        $table->string('status')->default('Aktif')->after('kelas_id');
        $table->year('tahun_lulus')->nullable()->after('status');
    });
}

public function down()
{
    Schema::table('siswas', function (Blueprint $table) {
        $table->dropColumn(['status', 'tahun_lulus']);
    });
}

}
