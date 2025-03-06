<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReporteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reporte', function (Blueprint $table) {
            $table->id();
            $table->timestamp('inicio', 6)->nullable(false);  // Columna inicio
            $table->timestamp('fin', 6)->nullable(false);     // Columna fin
            $table->string('sat_id', 255)->nullable(false);   // Columna sat_id
            $table->bigInteger('state_id')->nullable(false);  // Columna state_id (int8)
            $table->string('city_id', 255)->nullable(false);  // Columna city_id
            $table->bigInteger('modalidad_id')->nullable(false); // Columna modalidad_id (int8)
            $table->integer('stage_id')->nullable(false);     // Columna stage_id (int4)
            $table->timestamps(); // columnas created_at y updated_at (opcional)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reporte');
    }
}
