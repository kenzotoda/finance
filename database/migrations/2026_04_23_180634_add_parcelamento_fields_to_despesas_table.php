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
        Schema::table('despesas', function (Blueprint $table) {
            $table->uuid('compra_parcelada_id')->nullable()->after('despesa_fixa_id');
            $table->unsignedSmallInteger('parcela_atual')->nullable()->after('compra_parcelada_id');
            $table->unsignedSmallInteger('total_parcelas')->nullable()->after('parcela_atual');
            $table->index('compra_parcelada_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('despesas', function (Blueprint $table) {
            $table->dropIndex(['compra_parcelada_id']);
            $table->dropColumn(['compra_parcelada_id', 'parcela_atual', 'total_parcelas']);
        });
    }
};
