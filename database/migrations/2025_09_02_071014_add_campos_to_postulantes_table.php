<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposToPostulantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postulantes', function (Blueprint $table) {
            $table->integer('nivel')->nullable(); // Agregar el campo 'nivel'
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
            $table->dropColumn('nivel'); // Eliminar el campo 'nivel'
        });
    }
}
