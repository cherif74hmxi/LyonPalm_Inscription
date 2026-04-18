<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adhesions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adherent_id')->constrained('adherents')->onDelete('cascade');
            $table->foreignId('saison_id')->constrained('saisons')->onDelete('cascade');
            $table->foreignId('type_adhesion_id')->constrained('types_adhesion')->onDelete('cascade');
            $table->decimal('montant_total', 10, 2);
            $table->decimal('montant_paye', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adhesions');
    }
};
