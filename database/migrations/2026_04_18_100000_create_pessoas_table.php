<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cpf_cnpj', 20)->nullable()->unique();
            $table->enum('tipo', ['lead', 'prospect', 'cliente'])->default('lead');
            $table->string('telefone', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->enum('estado_civil', ['solteiro', 'casado', 'divorciado', 'viuvo', 'uniao_estavel', 'separado'])->nullable();
            $table->string('profissao')->nullable();
            $table->string('nacionalidade')->nullable()->default('Brasileiro(a)');
            $table->string('rg', 30)->nullable();
            $table->string('orgao_emissor_rg', 30)->nullable();
            $table->string('origem')->nullable()->comment('Como chegou: indicação, site, evento, etc.');
            $table->text('obs')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
};
