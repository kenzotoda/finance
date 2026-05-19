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
        Schema::table('receitas', function (Blueprint $table) {
            $table->unsignedBigInteger('lucro_fixo_id')->nullable()->after('categoria_id');
            $table->unique(['lucro_fixo_id', 'data']);
            $table->index('lucro_fixo_id');
        });

        Schema::table('receitas', function (Blueprint $table) {
            $table->foreign('lucro_fixo_id')->references('id')->on('lucro_fixos')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receitas', function (Blueprint $table) {
            $table->dropForeign(['lucro_fixo_id']);
            $table->dropUnique(['lucro_fixo_id', 'data']);
            $table->dropIndex(['lucro_fixo_id']);
            $table->dropColumn('lucro_fixo_id');
        });
    }
};
