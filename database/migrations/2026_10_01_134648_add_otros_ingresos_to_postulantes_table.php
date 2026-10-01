<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtrosIngresosToPostulantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postulantes', function (Blueprint $table) {
            $table->integer('otros_ingresos')->nullable()->after('ingreso_familiar');
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
            $table->dropColumn('otros_ingresos');
        });
    }
}
