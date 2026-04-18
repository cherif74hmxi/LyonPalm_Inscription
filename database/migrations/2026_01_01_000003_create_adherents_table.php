<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adherents', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance');
            $table->enum('sexe', ['M', 'F', 'Autre'])->nullable();
            $table->string('email')->unique();
            $table->string('adresse');
            $table->string('code_postal');
            $table->string('ville');
            $table->string('telephone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('contact_urgence_nom');
            $table->string('contact_urgence_telephone');
            $table->string('photo')->nullable();
            $table->enum('statut', ['actif', 'archive'])->default('actif');
            $table->boolean('rgpd_accepte')->default(false);
            $table->string('rgpd_ip')->nullable();
            $table->foreignId('representant_legal_id')->nullable()->constrained('representants_legaux')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adherents');
    }
};
