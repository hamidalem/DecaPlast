<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bon_ventes', function (Blueprint $table) {
            $table->float('montant_total', 10, 2)->default(0);
            $table->float('montant_verse', 10, 2)->default(0);
            $table->float('reste_a_payer', 10, 2)->virtualAs('montant_total - montant_verse');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bon_ventes', function (Blueprint $table) {
            //
        });
    }
};
