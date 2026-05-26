<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contas_pagar_receber', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('tipo', 10);
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->decimal('valor', 12, 2);
            $table->date('data');
            $table->uuid('grupo_parcelamento_id')->nullable()->index();
            $table->unsignedTinyInteger('parcela_atual')->nullable();
            $table->unsignedTinyInteger('total_parcelas')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'tipo', 'data']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contas_pagar_receber');
    }
};
