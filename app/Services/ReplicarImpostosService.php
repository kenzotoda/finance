<?php

namespace App\Services;

use App\Models\Despesa;
use App\Models\Imposto;
use App\Models\User;
use Carbon\Carbon;

class ReplicarImpostosService
{
    public function execute(User $user, ?Carbon $mesReferencia = null): void
    {
        $mesReferencia ??= now()->startOfMonth();

        $impostos = $user->impostos()
            ->where('ativa', true)
            ->get();

        foreach ($impostos as $imposto) {
            if ($imposto->periodicidade === Imposto::PERIODICIDADE_ANUAL) {
                if (
                    empty($imposto->renovacao_mes) ||
                    empty($imposto->renovacao_ano) ||
                    (int) $imposto->renovacao_mes !== $mesReferencia->month ||
                    $mesReferencia->year < (int) $imposto->renovacao_ano
                ) {
                    continue;
                }

                $data = $mesReferencia->copy()->day(1);
            } else {
                $data = $mesReferencia->copy()->day(min($imposto->dia_vencimento, $mesReferencia->daysInMonth));
            }

            Despesa::firstOrCreate(
                [
                    'imposto_id' => $imposto->id,
                    'data' => $data->toDateString(),
                ],
                [
                    'user_id' => $user->id,
                    'categoria_id' => $imposto->categoria_id,
                    'titulo' => $imposto->titulo,
                    'valor' => $imposto->valor,
                    'tipo' => 'imposto',
                    'descricao' => $imposto->descricao,
                ]
            );
        }
    }
}
