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
        Schema::create('quantite_achats', function (Blueprint $table) {
            $table->unsignedInteger('n_ba');
            $table->unsignedInteger('id_prod');
            $table->integer('qte_achat')->nullable();
            $table->float('prix_achat')->nullable();
            $table->primary(['n_ba', 'id_prod']);

            // This line is updated to include onDelete('cascade')
            $table->foreign('n_ba')->references('n_ba')->on('bon_achats')->onDelete('cascade');

            $table->foreign('id_prod')->references('id_prod')->on('produits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quantite_achats');
    }
};
