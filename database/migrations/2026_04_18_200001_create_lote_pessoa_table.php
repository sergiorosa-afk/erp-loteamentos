<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lote_pessoa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->cascadeOnDelete();
            $table->foreignId('pessoa_id')->constrained('pessoas')->cascadeOnDelete();
            $table->enum('papel', ['proprietario', 'comprador', 'interessado'])->default('interessado');
            $table->date('data_vinculo')->nullable();
            $table->text('obs')->nullable();
            $table->timestamps();

            // Um mesmo par lote+pessoa só pode ter um papel
            $table->unique(['lote_id', 'pessoa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lote_pessoa');
    }
};
