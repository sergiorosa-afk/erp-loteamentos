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
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quadra_id')->constrained('quadras')->cascadeOnDelete();
            $table->string('numero');
            $table->string('codigo_interno')->nullable();
            $table->decimal('area', 15, 2)->nullable();
            $table->decimal('testada', 10, 2)->nullable();
            $table->decimal('comprimento', 10, 2)->nullable();
            $table->enum('situacao', ['disponivel', 'reservado', 'vendido', 'permutado'])->default('disponivel');
            $table->boolean('unificado')->default(false);
            $table->json('lotes_originais')->nullable();
            $table->text('restricoes')->nullable();
            $table->decimal('valor_tabela', 15, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['quadra_id', 'numero']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
