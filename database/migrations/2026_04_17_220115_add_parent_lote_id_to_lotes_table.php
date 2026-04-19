<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->foreignId('parent_lote_id')
                ->nullable()
                ->after('quadra_id')
                ->constrained('lotes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->dropForeign(['parent_lote_id']);
            $table->dropColumn('parent_lote_id');
        });
    }
};
