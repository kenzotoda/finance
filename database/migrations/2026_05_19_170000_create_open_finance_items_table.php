<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('open_finance_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('pluggy_item_id')->unique();
            $table->string('connector_id')->nullable();
            $table->string('connector_name')->nullable();
            $table->string('status')->nullable();
            $table->string('execution_status')->nullable();
            $table->string('last_error_code')->nullable();
            $table->text('last_error_message')->nullable();
            $table->timestamp('pluggy_updated_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('open_finance_items');
    }
};
