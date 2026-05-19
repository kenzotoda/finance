<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Despesa;
use App\Models\DespesaFixa;
use App\Models\Receita;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FinanceiroSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@financeiro.com'],
            [
                'name' => 'Usuario Demo',
                'password' => bcrypt('password'),
            ]
        );

        $categorias = collect(['Alimentacao', 'Transporte', 'Lazer', 'Moradia', 'Salario'])
            ->mapWithKeys(function (string $nome) use ($user) {
                $categoria = Categoria::firstOrCreate([
                    'user_id' => $user->id,
                    'nome' => $nome,
                ]);

                return [$nome => $categoria];
            });

        $despesasFixas = [
            ['titulo' => 'Aluguel', 'valor' => 1200, 'dia' => 5, 'categoria' => 'Moradia'],
            ['titulo' => 'Internet', 'valor' => 120, 'dia' => 10, 'categoria' => 'Moradia'],
        ];

        foreach ($despesasFixas as $item) {
            $despesaFixa = DespesaFixa::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'titulo' => $item['titulo'],
                ],
                [
                    'categoria_id' => $categorias[$item['categoria']]->id,
                    'valor' => $item['valor'],
                    'dia_vencimento' => $item['dia'],
                    'ativa' => true,
                ]
            );

            for ($mes = 1; $mes <= 3; $mes++) {
                $data = Carbon::create(now()->year, $mes, min($item['dia'], 28));
                Despesa::firstOrCreate(
                    ['despesa_fixa_id' => $despesaFixa->id, 'data' => $data->toDateString()],
                    [
                        'user_id' => $user->id,
                        'categoria_id' => $categorias[$item['categoria']]->id,
                        'titulo' => $item['titulo'],
                        'valor' => $item['valor'],
                        'tipo' => 'fixa',
                    ]
                );
            }
        }

        for ($mes = 1; $mes <= 3; $mes++) {
            Receita::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'titulo' => 'Salario mensal',
                    'data' => Carbon::create(now()->year, $mes, 5)->toDateString(),
                ],
                [
                    'categoria_id' => $categorias['Salario']->id,
                    'valor' => 4500,
                ]
            );

            Despesa::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'titulo' => 'Supermercado',
                    'data' => Carbon::create(now()->year, $mes, 12)->toDateString(),
                ],
                [
                    'categoria_id' => $categorias['Alimentacao']->id,
                    'valor' => 650,
                    'tipo' => 'variavel',
                ]
            );
        }
    }
}
