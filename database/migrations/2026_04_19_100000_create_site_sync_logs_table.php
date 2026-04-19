<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imovel_id')->constrained('imoveis')->cascadeOnDelete();
            $table->string('evento', 50)->default('saved'); // saved | deleted | manual
            $table->string('status', 20)->default('pendente'); // pendente | sucesso | erro
            $table->json('payload')->nullable();
            $table->text('resposta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_sync_logs');
    }
};
