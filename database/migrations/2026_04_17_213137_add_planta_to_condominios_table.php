<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('condominios', function (Blueprint $table) {
            $table->string('planta_path')->nullable()->after('email');
            $table->string('planta_nome_original')->nullable()->after('planta_path');
        });
    }

    public function down(): void
    {
        Schema::table('condominios', function (Blueprint $table) {
            $table->dropColumn(['planta_path', 'planta_nome_original']);
        });
    }
};
