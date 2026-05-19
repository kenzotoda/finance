<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE despesas MODIFY COLUMN tipo ENUM('fixa', 'variavel', 'imposto', 'open_finance_debito') NOT NULL");

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE despesas DROP CONSTRAINT IF EXISTS despesas_tipo_check");
            DB::statement("ALTER TABLE despesas ADD CONSTRAINT despesas_tipo_check CHECK (tipo IN ('fixa', 'variavel', 'imposto', 'open_finance_debito'))");
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE despesas MODIFY COLUMN tipo ENUM('fixa', 'variavel') NOT NULL");

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE despesas DROP CONSTRAINT IF EXISTS despesas_tipo_check");
            DB::statement("ALTER TABLE despesas ADD CONSTRAINT despesas_tipo_check CHECK (tipo IN ('fixa', 'variavel'))");
        }
    }
};
