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
        Schema::create('condominios', function (Blueprint $table) {
            $table->id();

            // Identificação
            $table->string('nome');
            $table->string('cnpj', 18)->nullable()->unique();
            $table->string('matricula_cartorio')->nullable();
            $table->string('numero_registro')->nullable();

            // Endereço
            $table->string('logradouro')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('municipio_registro')->nullable();

            // Características físicas
            $table->decimal('area_total', 15, 2)->nullable();
            $table->decimal('area_verde', 15, 2)->nullable();
            $table->decimal('area_vias', 15, 2)->nullable();
            $table->integer('total_quadras')->nullable();
            $table->integer('total_lotes')->nullable();
            $table->enum('zoneamento', ['residencial', 'comercial', 'misto'])->default('residencial');

            // Documentação
            $table->date('data_aprovacao_prefeitura')->nullable();
            $table->date('data_registro_cartorio')->nullable();

            // Contato/Responsável
            $table->string('incorporadora')->nullable();
            $table->string('sindico')->nullable();
            $table->string('administradora')->nullable();
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();

            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('condominios');
    }
};
