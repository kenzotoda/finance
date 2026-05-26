<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('despesas', function (Blueprint $table) {
            $table->foreignId('cartao_id')->nullable()->after('imposto_id')->constrained('cartoes')->nullOnDelete();
            $table->foreignId('fatura_cartao_id')->nullable()->after('cartao_id')->constrained('fatura_cartoes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('despesas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fatura_cartao_id');
            $table->dropConstrainedForeignId('cartao_id');
        });
    }
};
