<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bon_achats', function (Blueprint $table) {
            $table->string('statut_depot', 100)
                ->default('non_assigné')
                ->after('etat_ba');
        });
    }

    public function down()
    {
        Schema::table('bon_achats', function (Blueprint $table) {
            $table->dropColumn('statut_depot');
        });
    }
};
