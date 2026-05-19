<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('despesa_fixas', function (Blueprint $table) {
            $table->unsignedTinyInteger('renovacao_mes')->nullable()->after('periodicidade');
            $table->unsignedSmallInteger('renovacao_ano')->nullable()->after('renovacao_mes');
        });

        DB::table('despesa_fixas')
            ->where('periodicidade', 'anual')
            ->update([
                'renovacao_mes' => DB::raw('MONTH(created_at)'),
                'renovacao_ano' => DB::raw('YEAR(created_at)'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('despesa_fixas', function (Blueprint $table) {
            $table->dropColumn(['renovacao_mes', 'renovacao_ano']);
        });
    }
};
