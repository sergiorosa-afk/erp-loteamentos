<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: modifica o ENUM para incluir 'apresentador'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','viewer','apresentador') NOT NULL DEFAULT 'viewer'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','viewer') NOT NULL DEFAULT 'viewer'");
    }
};
