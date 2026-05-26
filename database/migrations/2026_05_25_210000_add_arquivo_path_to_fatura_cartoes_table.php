<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fatura_cartoes', function (Blueprint $table) {
            $table->string('arquivo_path')->nullable()->after('arquivo_hash');
        });
    }

    public function down(): void
    {
        Schema::table('fatura_cartoes', function (Blueprint $table) {
            $table->dropColumn('arquivo_path');
        });
    }
};
