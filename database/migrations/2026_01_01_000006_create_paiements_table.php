<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adhesion_id')->constrained('adhesions')->onDelete('cascade');
            $table->decimal('montant', 10, 2);
            $table->enum('mode', ['Especes', 'Cheque', 'Virement', 'Carte', 'HelloAsso']);
            $table->date('date_paiement');
            $table->text('remarques')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
