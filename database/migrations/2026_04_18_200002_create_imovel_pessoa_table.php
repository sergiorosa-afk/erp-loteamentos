<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imovel_pessoa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imovel_id')->constrained('imoveis')->cascadeOnDelete();
            $table->foreignId('pessoa_id')->constrained('pessoas')->cascadeOnDelete();
            $table->enum('papel', ['proprietario', 'locatario'])->default('proprietario');
            $table->date('data_vinculo')->nullable();
            $table->text('obs')->nullable();
            $table->timestamps();

            $table->unique(['imovel_id', 'pessoa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imovel_pessoa');
    }
};
