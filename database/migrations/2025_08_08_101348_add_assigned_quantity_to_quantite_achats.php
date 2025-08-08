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
        Schema::table('quantite_achats', function (Blueprint $table) {
            $table->integer('assigned_quantity')->default(0)->after('prix_achat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quantite_achats', function (Blueprint $table) {
            //
        });
    }
};
