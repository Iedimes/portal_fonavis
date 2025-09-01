<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrigenToDighObservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('digh_observations', function (Blueprint $table) {
            $table->unsignedTinyInteger('origen')
                ->nullable() // <-- Permite null
                ->comment('1 = VTA, 2 = ETH')
                ->after('observation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('digh_observations', function (Blueprint $table) {
            $table->dropColumn('origen');
        });
    }
}
