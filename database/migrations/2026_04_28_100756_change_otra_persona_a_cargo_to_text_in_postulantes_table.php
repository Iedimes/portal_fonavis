<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

class ChangeOtraPersonaACargoToTextInPostulantesTable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE postulantes DROP CONSTRAINT IF EXISTS postulantes_otra_persona_a_cargo_check');
        DB::statement('ALTER TABLE postulantes ALTER COLUMN otra_persona_a_cargo TYPE TEXT');
        DB::statement("UPDATE postulantes SET otra_persona_a_cargo = NULL WHERE otra_persona_a_cargo = 'N'");
    }

    public function down()
    {
        DB::statement('ALTER TABLE postulantes ALTER COLUMN otra_persona_a_cargo TYPE VARCHAR(255)');
    }
}
