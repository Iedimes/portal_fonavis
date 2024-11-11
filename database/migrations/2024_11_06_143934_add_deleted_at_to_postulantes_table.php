<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddDeletedAtToPostulantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('postulantes', function (Blueprint $table) {
        $table->softDeletes();
    });
}

public function down()
{
    Schema::table('postulantes', function (Blueprint $table) {
        $table->dropSoftDeletes();
    });
}

}
