<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fatura_cartoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cartao_id')->constrained('cartoes')->cascadeOnDelete();
            $table->date('competencia');
            $table->string('arquivo_nome');
            $table->string('arquivo_hash', 64);
            $table->unsignedInteger('total_lancamentos')->default(0);
            $table->decimal('total_valor', 12, 2)->default(0);
            $table->string('status', 20)->default('importada');
            $table->timestamps();

            $table->unique(['cartao_id', 'competencia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fatura_cartoes');
    }
};
