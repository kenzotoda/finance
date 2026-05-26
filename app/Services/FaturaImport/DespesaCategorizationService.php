<?php

namespace App\Services\FaturaImport;

use App\Models\Categoria;
use App\Models\User;
use Illuminate\Support\Str;

class DespesaCategorizationService
{
    public function resolveCategoriaId(User $user, ?string $descricao): ?int
    {
        if (blank($descricao)) {
            return null;
        }

        $normalized = Str::of(Str::lower($descricao))
            ->ascii()
            ->replaceMatches('/[^a-z0-9\s]/', ' ')
            ->value();

        $keywordMap = [
            'imposto' => ['iptu', 'ipva', 'irpf', 'darf', 'inss', 'iss', 'tributo'],
            'moradia' => ['condominio', 'aluguel', 'energia', 'luz', 'agua', 'gas'],
            'transporte' => ['combustivel', 'uber', '99', 'onibus', 'metro', 'estacionamento', 'pedagio'],
            'saude' => ['farmacia', 'plano de saude', 'consulta', 'hospital', 'laboratorio'],
            'alimentacao' => ['mercado', 'supermercado', 'ifood', 'restaurante', 'padaria', 'acougue'],
            'educacao' => ['escola', 'curso', 'faculdade', 'livraria'],
        ];

        foreach ($keywordMap as $categoriaNome => $keywords) {
            foreach ($keywords as $keyword) {
                if (! str_contains($normalized, Str::lower(Str::ascii($keyword)))) {
                    continue;
                }

                $categoria = $user->categorias()
                    ->whereRaw('LOWER(nome) = ?', [Str::lower($categoriaNome)])
                    ->first();

                if ($categoria instanceof Categoria) {
                    return $categoria->id;
                }
            }
        }

        return null;
    }
}
