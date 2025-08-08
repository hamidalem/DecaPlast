<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bon_achats', function (Blueprint $table) {
            $table->float('montant_total', 10, 2)->default(0);
            $table->float('montant_verse', 10, 2)->default(0);
            $table->float('reste_a_payer', 10, 2)->virtualAs('montant_total - montant_verse');
        });
    }

    public function down()
    {
        Schema::table('bon_achats', function (Blueprint $table) {
            $table->dropColumn(['montant_total', 'montant_verse', 'reste_a_payer']);
        });
    }
};
