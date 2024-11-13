<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotivosTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motivos', function (Blueprint $table) {
            $table->id();  // Clave primaria autoincremental
            $table->unsignedBigInteger('project_id');  // ID del proyecto
            $table->string('motivo');  // Descripción del motivo
            $table->timestamps();  // Timestamps para created_at y updated_at

            // Si deseas definir una relación de clave foránea:
            // $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Revierte las migraciones.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('motivos');
    }
}
