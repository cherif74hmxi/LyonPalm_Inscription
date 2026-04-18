<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('types_adhesion', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->foreignId('saison_id')->constrained('saisons')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('types_adhesion');
    }
};
