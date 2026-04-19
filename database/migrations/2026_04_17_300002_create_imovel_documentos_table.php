<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imovel_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imovel_id')->constrained('imoveis')->cascadeOnDelete();
            $table->string('tipo'); // matricula, escritura, iptu, certidao_negativa, habite_se, planta, condominio, procuracao, outro
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('path');
            $table->string('nome_original');
            $table->bigInteger('tamanho_bytes')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('numero_documento')->nullable();
            $table->string('orgao_emissor')->nullable();
            $table->date('data_emissao')->nullable();
            $table->date('data_vencimento')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imovel_documentos');
    }
};
