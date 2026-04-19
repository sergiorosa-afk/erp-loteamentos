<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imovel_pessoa_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imovel_id')->constrained('imoveis')->cascadeOnDelete();
            $table->foreignId('pessoa_id')->constrained()->cascadeOnDelete();
            $table->string('papel', 50);
            $table->string('acao', 20); // vinculado | desvinculado
            $table->date('data_vinculo')->nullable();     // data informada no formulário
            $table->decimal('valor_imovel', 15, 2)->nullable(); // snapshot valor_mercado
            $table->text('obs')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imovel_pessoa_historicos');
    }
};
