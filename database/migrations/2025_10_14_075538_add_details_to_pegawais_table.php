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
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('nama');
            $table->string('jk')->nullable()->after('foto'); // Jenis Kelamin
            $table->string('tempat_lahir')->nullable()->after('jk');
            $table->date('ttl')->nullable()->after('tempat_lahir'); // Tanggal Lahir
            $table->string('status_kepegawaian')->nullable()->after('jabatan');
            $table->text('alamat')->nullable()->after('status_kepegawaian');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn(['foto', 'jk', 'tempat_lahir', 'ttl', 'status_kepegawaian', 'alamat']);
        });
    }
};
