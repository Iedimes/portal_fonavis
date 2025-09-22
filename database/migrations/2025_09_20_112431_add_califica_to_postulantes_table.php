<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCalificaToPostulantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postulantes', function (Blueprint $table) {
            $table->enum('califica', ['S', 'N'])->nullable()->after('nivel'); // Permitir NULL
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
            $table->dropColumn('califica');
        });
    }
}
