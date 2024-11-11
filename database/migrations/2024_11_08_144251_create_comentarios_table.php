<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComentariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id(); // Crea un campo 'id' como clave primaria
            $table->unsignedBigInteger('postulante_id'); // Campo para el ID del postulante
            $table->string('cedula'); // Campo para la cédula
            $table->text('comentario'); // Campo para el comentario
            $table->timestamps(); // Campos para created_at y updated_at

            // Si quieres establecer una relación con la tabla postulantes, descomenta la siguiente línea
            // $table->foreign('postulante_id')->references('id')->on('postulantes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comentarios');
    }
}
