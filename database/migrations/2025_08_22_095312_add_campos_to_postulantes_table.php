<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('postulantes', function (Blueprint $table) {
            $table->enum('hijo_sosten', ['S', 'N'])->nullable()->after('nexp');
            $table->enum('otra_persona_a_cargo', ['S', 'N'])->nullable()->after('hijo_sosten');
            $table->text('composicion_del_grupo')->nullable()->after('otra_persona_a_cargo');
            $table->text('documentos_presentados')->nullable()->after('composicion_del_grupo');
            $table->enum('discapacidad', ['S', 'N'])->nullable()->after('documentos_presentados'); // Nuevo campo
            $table->enum('tercera_edad', ['S', 'N'])->nullable()->after('discapacidad'); // Nuevo campo
            $table->integer('ingreso_familiar')->nullable()->after('tercera_edad'); // Nuevo campo
            $table->integer('cantidad_hijos')->nullable()->after('ingreso_familiar'); // Nuevo campo
            $table->text('documentos_faltantes')->nullable()->after('cantidad_hijos');
            $table->text('observacion_de_consideracion')->nullable()->after('documentos_faltantes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postulantes', function (Blueprint $table) {
            $table->dropColumn([
                'hijo_sosten',
                'otra_persona_a_cargo',
                'composicion_del_grupo',
                'documentos_presentados',
                'discapacidad', // Eliminar en reversa
                'tercera_edad', // Eliminar en reversa
                'ingreso_familiar', // Eliminar en reversa
                'cantidad_hijos' // Eliminar en reversa
            ]);
        });
    }
};
