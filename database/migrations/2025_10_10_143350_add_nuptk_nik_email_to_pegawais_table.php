<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('nuptk')->nullable()->after('nip');
            $table->string('nik', 16)->nullable()->after('nuptk');
            $table->string('email')->nullable()->unique()->after('nik');
        });
    }

    public function down()
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn(['nuptk', 'nik', 'email']);
        });
    }
};
