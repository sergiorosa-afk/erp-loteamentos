<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source', 50)->default('site');
            $table->json('payload');
            $table->string('status', 20)->default('sucesso'); // sucesso | erro | duplicata
            $table->foreignId('pessoa_id')->nullable()->constrained()->nullOnDelete();
            $table->text('mensagem')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
