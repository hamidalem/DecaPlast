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
        Schema::table('quantite_ventes', function (Blueprint $table) {
            $table->integer('id_depot')->nullable()->after('id_prod');
        });
    }

    public function down()
    {
        Schema::table('quantite_ventes', function (Blueprint $table) {
            $table->dropColumn('id_depot');
        });
    }
};
