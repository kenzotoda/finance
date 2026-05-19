<?php

namespace App\Services;

use App\Models\LucroFixo;
use App\Models\Receita;
use App\Models\User;
use Carbon\Carbon;

class ReplicarLucrosFixosService
{
    public function execute(User $user, ?Carbon $mesReferencia = null): void
    {
        $mesReferencia ??= now()->startOfMonth();

        $lucrosFixos = $user->lucrosFixos()
            ->where('ativa', true)
            ->get();

        foreach ($lucrosFixos as $lucroFixo) {
            if ($lucroFixo->periodicidade === LucroFixo::PERIODICIDADE_ANUAL) {
                if (
                    empty($lucroFixo->renovacao_mes) ||
                    empty($lucroFixo->renovacao_ano) ||
                    (int) $lucroFixo->renovacao_mes !== $mesReferencia->month ||
                    $mesReferencia->year < (int) $lucroFixo->renovacao_ano
                ) {
                    continue;
                }

                $data = $mesReferencia->copy()->day(1);
            } else {
                $data = $mesReferencia->copy()->day(min($lucroFixo->dia_vencimento, $mesReferencia->daysInMonth));
            }

            Receita::firstOrCreate(
                [
                    'lucro_fixo_id' => $lucroFixo->id,
                    'data' => $data->toDateString(),
                ],
                [
                    'user_id' => $user->id,
                    'categoria_id' => $lucroFixo->categoria_id,
                    'titulo' => $lucroFixo->titulo,
                    'valor' => $lucroFixo->valor,
                    'descricao' => $lucroFixo->descricao,
                ]
            );
        }
    }
}
