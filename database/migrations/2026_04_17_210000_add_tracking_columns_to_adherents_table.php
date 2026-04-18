<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adherents', function (Blueprint $table) {
            $table->timestamp('rgpd_accepte_le')->nullable();
            $table->timestamp('archive_le')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('adherents', function (Blueprint $table) {
            $table->dropColumn(['rgpd_accepte_le', 'archive_le']);
        });
    }
};
