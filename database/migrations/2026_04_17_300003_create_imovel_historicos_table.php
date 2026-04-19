<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imovel_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imovel_id')->constrained('imoveis')->cascadeOnDelete();
            $table->string('tipo'); // compra, venda, avaliacao, reforma, locacao, permuta, inventario, outro
            $table->date('data')->nullable();
            $table->decimal('valor', 15, 2)->nullable();
            $table->string('proprietario_anterior')->nullable();
            $table->string('cpf_cnpj_anterior')->nullable();
            $table->string('proprietario_atual')->nullable();
            $table->string('cpf_cnpj_atual')->nullable();
            $table->string('cartorio')->nullable();
            $table->string('numero_escritura')->nullable();
            $table->string('numero_registro')->nullable();
            $table->string('corretor')->nullable();
            $table->text('descricao')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imovel_historicos');
    }
};
