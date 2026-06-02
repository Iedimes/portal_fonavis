<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

class ChangeHijoSostenToTextInPostulantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the check constraint created by Laravel's enum on PostgreSQL
        DB::statement('ALTER TABLE postulantes DROP CONSTRAINT IF EXISTS postulantes_hijo_sosten_check');
        
        // Change the column type to text
        DB::statement('ALTER TABLE postulantes ALTER COLUMN hijo_sosten TYPE TEXT');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Change back to string (though reverting to enum 'S'/'N' would require data cleaning)
        DB::statement('ALTER TABLE postulantes ALTER COLUMN hijo_sosten TYPE VARCHAR(255)');
    }
}
