<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pessoa_certidoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pessoa_id')->constrained('pessoas')->cascadeOnDelete();
            $table->enum('tipo', [
                'rg', 'cpf', 'cnh', 'comprovante_residencia',
                'certidao_nascimento', 'certidao_casamento',
                'certidao_obito', 'procuracao', 'outro',
            ])->default('outro');
            $table->string('titulo')->nullable();
            $table->string('numero_documento', 100)->nullable();
            $table->string('orgao_emissor', 100)->nullable();
            $table->date('data_emissao')->nullable();
            $table->date('data_vencimento')->nullable();
            $table->string('arquivo_path');
            $table->string('nome_original');
            $table->unsignedBigInteger('tamanho_bytes')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pessoa_certidoes');
    }
};
