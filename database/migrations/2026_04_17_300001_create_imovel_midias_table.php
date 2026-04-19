<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imovel_midias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imovel_id')->constrained('imoveis')->cascadeOnDelete();
            $table->string('tipo'); // imagem, video, pdf
            $table->string('titulo')->nullable();
            $table->text('descricao')->nullable();
            $table->string('path');
            $table->string('nome_original');
            $table->bigInteger('tamanho_bytes')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->boolean('capa')->default(false); // foto principal
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imovel_midias');
    }
};
