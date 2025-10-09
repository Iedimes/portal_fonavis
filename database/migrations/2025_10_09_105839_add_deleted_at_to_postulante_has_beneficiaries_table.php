<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToPostulanteHasBeneficiariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postulante_has_beneficiaries', function (Blueprint $table) {
            if (!Schema::hasColumn('postulante_has_beneficiaries', 'deleted_at')) {
                $table->softDeletes()->after('updated_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('postulante_has_beneficiaries', function (Blueprint $table) {
            if (Schema::hasColumn('postulante_has_beneficiaries', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
}
