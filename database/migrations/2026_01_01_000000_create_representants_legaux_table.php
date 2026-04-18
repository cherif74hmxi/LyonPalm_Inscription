<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('representants_legaux', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('telephone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('lien_parental')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('representants_legaux');
    }
};
