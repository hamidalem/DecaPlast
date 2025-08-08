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
        Schema::create('bon_ventes', function (Blueprint $table) {
            $table->increments('n_bv');
            $table->date('date_bv')->nullable();
            $table->enum('etat_bv', ['paye', 'verse'])->nullable();
            $table->string('nom_client', 50)->nullable();
            $table->foreign('nom_client')->references('nom_client')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bon_ventes');
    }
};
