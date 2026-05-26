<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('despesas', function (Blueprint $table) {
            $table->string('origem', 20)->default('manual')->after('tipo');
            $table->string('fatura_arquivo')->nullable()->after('origem');
            $table->string('fatura_hash', 64)->nullable()->after('fatura_arquivo');
            $table->string('hash_lancamento', 64)->nullable()->after('fatura_hash');

            $table->index(['user_id', 'origem']);
            $table->index('hash_lancamento');
        });
    }

    public function down(): void
    {
        Schema::table('despesas', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'origem']);
            $table->dropIndex(['hash_lancamento']);
            $table->dropColumn(['origem', 'fatura_arquivo', 'fatura_hash', 'hash_lancamento']);
        });
    }
};
