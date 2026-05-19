<?php

namespace App\Services\OpenFinance;

use App\Models\Categoria;
use App\Models\User;
use Illuminate\Support\Str;

class TransactionCategorizationService
{
    /**
     * @return array{categoria_id:int|null, auto_categorized:bool}
     */
    public function categorize(User $user, ?string $description): array
    {
        if (blank($description)) {
            return ['categoria_id' => null, 'auto_categorized' => false];
        }

        $normalizedDescription = Str::of(Str::lower($description))
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

        foreach ($keywordMap as $categoryName => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($normalizedDescription, Str::lower(Str::ascii($keyword)))) {
                    $category = $user->categorias()
                        ->whereRaw('LOWER(nome) = ?', [Str::lower($categoryName)])
                        ->first();

                    if ($category instanceof Categoria) {
                        return ['categoria_id' => $category->id, 'auto_categorized' => true];
                    }
                }
            }
        }

        return ['categoria_id' => null, 'auto_categorized' => false];
    }
}
