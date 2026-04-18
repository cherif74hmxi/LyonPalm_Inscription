<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saisons', function (Blueprint $table) {
            $table->id();
            $table->integer('annee_debut');
            $table->integer('annee_fin');
            $table->boolean('active')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saisons');
    }
};
