<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_users_dependencies', function (Blueprint $table) {
            $table->id();
            $table->integer('admin_user_id');
            $table->foreign('admin_user_id')->references('id')->on('admin_users');
            $table->integer('dependency_id');
            $table->foreign('dependency_id')->references('id')->on('dependencies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_users_dependencies');
    }
};
