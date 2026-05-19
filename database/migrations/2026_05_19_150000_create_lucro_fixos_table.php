<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lucro_fixos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('titulo');
            $table->decimal('valor', 12, 2);
            $table->unsignedTinyInteger('dia_vencimento')->default(1);
            $table->string('periodicidade', 10)->default('mensal');
            $table->unsignedTinyInteger('renovacao_mes')->nullable();
            $table->unsignedSmallInteger('renovacao_ano')->nullable();
            $table->text('descricao')->nullable();
            $table->boolean('ativa')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lucro_fixos');
    }
};
