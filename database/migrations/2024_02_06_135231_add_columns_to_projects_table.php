<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            //$table->bigInteger('res_nro')->nullable();
            //$table->date('fechares')->nullable();
            //$table->char('coordenadax')->nullable();
            //$table->char('coordenaday')->nullable();
            //$table->string('finca_nro')->nullable();
            $table->char('ubicacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('res_nro');
            $table->dropColumn('fechares');
            $table->dropColumn('coordenadax');
            $table->dropColumn('coordenaday');
            $table->dropColumn('finca_nro');
            $table->dropColumn('ubicacion');
        });
    }
}
