<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('open_finance_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('open_finance_item_id')->constrained()->cascadeOnDelete();
            $table->string('pluggy_account_id')->unique();
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('subtype')->nullable();
            $table->char('currency_code', 3)->nullable();
            $table->decimal('balance', 14, 2)->nullable();
            $table->timestamp('balance_updated_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('open_finance_accounts');
    }
};
