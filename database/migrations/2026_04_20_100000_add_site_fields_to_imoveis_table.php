<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imoveis', function (Blueprint $table) {
            $table->unsignedInteger('site_imovel_id')->nullable()->after('observacoes');
            $table->timestamp('site_sincronizado_em')->nullable()->after('site_imovel_id');
        });
    }

    public function down(): void
    {
        Schema::table('imoveis', function (Blueprint $table) {
            $table->dropColumn(['site_imovel_id', 'site_sincronizado_em']);
        });
    }
};
