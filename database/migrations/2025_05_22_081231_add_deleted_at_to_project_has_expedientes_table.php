<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToProjectHasExpedientesTable extends Migration
{
    public function up()
    {
        Schema::table('project_has_expedientes', function (Blueprint $table) {
            $table->softDeletes(); // agrega el campo deleted_at como nullable timestamp
        });
    }

    public function down()
    {
        Schema::table('project_has_expedientes', function (Blueprint $table) {
            $table->dropSoftDeletes(); // elimina el campo deleted_at
        });
    }
}

