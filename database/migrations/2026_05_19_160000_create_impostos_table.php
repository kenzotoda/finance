<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impostos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('tipo', 20);
            $table->string('titulo');
            $table->decimal('valor', 12, 2);
            $table->unsignedTinyInteger('dia_vencimento')->default(1);
            $table->string('periodicidade', 10)->default('anual');
            $table->unsignedTinyInteger('renovacao_mes')->nullable();
            $table->unsignedSmallInteger('renovacao_ano')->nullable();
            $table->text('descricao')->nullable();
            $table->boolean('ativa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impostos');
    }
};
