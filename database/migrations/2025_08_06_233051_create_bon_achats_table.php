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
        Schema::create('bon_achats', function (Blueprint $table) {
            $table->increments('n_ba');
            $table->date('date_ba')->nullable();
            $table->enum('etat_ba', ['paye', 'verse'])->nullable();
            $table->string('nom_fourn', 50)->nullable();
            $table->foreign('nom_fourn')->references('nom_fourn')->on('fournisseurs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bon_achats');
    }
};
