<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('despesas', function (Blueprint $table) {
            $table->unsignedBigInteger('imposto_id')->nullable()->after('despesa_fixa_id');
            $table->index('imposto_id');
        });

        Schema::table('despesas', function (Blueprint $table) {
            $table->foreign('imposto_id')->references('id')->on('impostos')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('despesas', function (Blueprint $table) {
            $table->dropForeign(['imposto_id']);
            $table->dropIndex(['imposto_id']);
            $table->dropColumn('imposto_id');
        });
    }
};
