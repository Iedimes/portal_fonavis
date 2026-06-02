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
        Schema::table('postulantes', function (Blueprint $table) {
            $table->string('motivo', 100)->nullable()->after('observacion_de_consideracion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('postulantes', function (Blueprint $table) {
            $table->dropColumn('motivo');
        });
    }
};
