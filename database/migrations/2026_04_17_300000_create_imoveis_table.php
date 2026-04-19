<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imoveis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->nullable()->constrained('lotes')->nullOnDelete();

            // Identificação
            $table->string('tipo')->nullable(); // casa, apartamento, terreno, galpao, outro
            $table->string('nome')->nullable();
            $table->text('descricao')->nullable();

            // Áreas
            $table->decimal('area_total', 15, 2)->nullable();
            $table->decimal('area_construida', 15, 2)->nullable();
            $table->decimal('area_privativa', 15, 2)->nullable();

            // Características
            $table->unsignedSmallInteger('quartos')->nullable();
            $table->unsignedSmallInteger('suites')->nullable();
            $table->unsignedSmallInteger('banheiros')->nullable();
            $table->unsignedSmallInteger('vagas_garagem')->nullable();
            $table->unsignedSmallInteger('andares')->nullable();
            $table->year('ano_construcao')->nullable();
            $table->string('padrao_acabamento')->nullable(); // simples, medio, alto, luxo
            $table->boolean('condominio_fechado')->nullable();
            $table->text('caracteristicas')->nullable(); // JSON de amenidades (piscina, churrasqueira, etc)

            // Registro / Cartório
            $table->string('matricula_imovel')->nullable();
            $table->string('inscricao_municipal')->nullable(); // IPTU
            $table->string('cartorio')->nullable();
            $table->string('numero_escritura')->nullable();
            $table->string('livro_escritura')->nullable();
            $table->string('folha_escritura')->nullable();

            // Endereço (pode diferir do condomínio)
            $table->string('logradouro')->nullable();
            $table->string('numero_endereco')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();
            $table->string('cep', 9)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Financeiro
            $table->decimal('valor_venal', 15, 2)->nullable();
            $table->decimal('valor_mercado', 15, 2)->nullable();
            $table->decimal('valor_iptu_anual', 15, 2)->nullable();
            $table->date('data_ultima_avaliacao')->nullable();

            // Situação
            $table->string('situacao_ocupacao')->nullable(); // desocupado, ocupado_proprietario, locado, outros
            $table->text('observacoes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imoveis');
    }
};
