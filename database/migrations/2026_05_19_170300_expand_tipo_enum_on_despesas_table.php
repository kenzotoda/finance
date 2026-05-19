<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE despesas MODIFY COLUMN tipo ENUM('fixa', 'variavel', 'imposto', 'open_finance_debito') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE despesas MODIFY COLUMN tipo ENUM('fixa', 'variavel', 'imposto') NOT NULL");
    }
};
