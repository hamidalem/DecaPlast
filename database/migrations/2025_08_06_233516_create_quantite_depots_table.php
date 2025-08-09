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
        Schema::create('quantite_depot', function (Blueprint $table) {
            $table->unsignedInteger('id_depot');
            $table->unsignedInteger('id_prod');
            $table->integer('qte_depot')->nullable();
            $table->primary(['id_depot', 'id_prod']);
            $table->foreign('id_depot')->references('id_depot')->on('depots');
            $table->foreign('id_prod')->references('id_prod')->on('produits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quantite_depot');
    }
};
