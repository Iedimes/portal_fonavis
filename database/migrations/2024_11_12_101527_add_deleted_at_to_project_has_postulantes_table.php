<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToProjectHasPostulantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_has_postulantes', function (Blueprint $table) {
            $table->softDeletes(); // Esto agrega el campo deleted_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_has_postulantes', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Esto elimina el campo deleted_at
        });
    }
}
