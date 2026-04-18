<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificats_medicaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adherent_id')->constrained('adherents')->onDelete('cascade');
            $table->date('date_emission');
            $table->date('date_expiration');
            $table->string('fichier')->nullable();
            $table->text('restrictions')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificats_medicaux');
    }
};
