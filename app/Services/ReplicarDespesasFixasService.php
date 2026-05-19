<?php

namespace App\Services;

use App\Models\DespesaFixa;
use App\Models\Despesa;
use App\Models\User;
use Carbon\Carbon;

class ReplicarDespesasFixasService
{
    public function execute(User $user, ?Carbon $mesReferencia = null): void
    {
        $mesReferencia ??= now()->startOfMonth();

        $despesasFixas = $user->despesasFixas()
            ->where('ativa', true)
            ->get();

        foreach ($despesasFixas as $despesaFixa) {
            if ($despesaFixa->periodicidade === DespesaFixa::PERIODICIDADE_ANUAL) {
                if (
                    empty($despesaFixa->renovacao_mes) ||
                    empty($despesaFixa->renovacao_ano) ||
                    (int) $despesaFixa->renovacao_mes !== $mesReferencia->month ||
                    $mesReferencia->year < (int) $despesaFixa->renovacao_ano
                ) {
                    continue;
                }

                $data = $mesReferencia->copy()->day(1);
            } else {
                $data = $mesReferencia->copy()->day(min($despesaFixa->dia_vencimento, $mesReferencia->daysInMonth));
            }

            Despesa::firstOrCreate(
                [
                    'despesa_fixa_id' => $despesaFixa->id,
                    'data' => $data->toDateString(),
                ],
                [
                    'user_id' => $user->id,
                    'categoria_id' => $despesaFixa->categoria_id,
                    'titulo' => $despesaFixa->titulo,
                    'valor' => $despesaFixa->valor,
                    'tipo' => 'fixa',
                    'descricao' => $despesaFixa->descricao,
                ]
            );
        }
    }
}
