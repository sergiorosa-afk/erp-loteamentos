<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quadras', function (Blueprint $table) {
            $table->json('poligono')->nullable()->after('observacoes');
        });

        Schema::table('lotes', function (Blueprint $table) {
            $table->json('poligono')->nullable()->after('valor_tabela');
        });
    }

    public function down(): void
    {
        Schema::table('quadras', function (Blueprint $table) {
            $table->dropColumn('poligono');
        });

        Schema::table('lotes', function (Blueprint $table) {
            $table->dropColumn('poligono');
        });
    }
};
