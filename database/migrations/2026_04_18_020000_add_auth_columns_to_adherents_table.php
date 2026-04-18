<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adherents', function (Blueprint $table) {
            if (! Schema::hasColumn('adherents', 'password')) {
                $table->string('password')->nullable()->after('email');
            }

            if (! Schema::hasColumn('adherents', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    public function down(): void
    {
        Schema::table('adherents', function (Blueprint $table) {
            if (Schema::hasColumn('adherents', 'remember_token')) {
                $table->dropRememberToken();
            }

            if (Schema::hasColumn('adherents', 'password')) {
                $table->dropColumn('password');
            }
        });
    }
};
