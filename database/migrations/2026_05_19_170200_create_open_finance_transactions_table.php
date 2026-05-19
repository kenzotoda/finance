<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('open_finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('open_finance_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('pluggy_transaction_id')->unique();
            $table->string('description')->nullable();
            $table->decimal('amount', 14, 2);
            $table->char('currency_code', 3)->nullable();
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->boolean('auto_categorized')->default(false);
            $table->timestamp('synced_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('open_finance_transactions');
    }
};
