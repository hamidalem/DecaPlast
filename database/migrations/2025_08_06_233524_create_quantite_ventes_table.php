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
        Schema::create('quantite_ventes', function (Blueprint $table) {
            $table->integer('n_bv');
            $table->integer('id_prod');
            $table->integer('qte_vente')->nullable();
            $table->float('prix_vente')->nullable();
            $table->primary(['n_bv', 'id_prod']);
            $table->foreign('n_bv')->references('n_bv')->on('bon_ventes');
            $table->foreign('id_prod')->references('id_prod')->on('produits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quantite_ventes');
    }
};
