<?php

namespace App\Http\Controllers;

use App\Exports\RelatorioLancamentosExport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class RelatorioController extends Controller
{
    public function index(): HttpResponse
    {
        $dados = $this->montarDadosRelatorio(Auth::user());

        return response()
            ->view('relatorios.index', $dados)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    public function exportExcel(): BinaryFileResponse
    {
        $dados = $this->montarDadosRelatorio(Auth::user());
        $arquivo = 'relatorio-financeiro-'.$dados['competencia'].'-'.Date::now()->format('His').'.xlsx';

        return Excel::download(
            new RelatorioLancamentosExport($dados['lancamentos']),
            $arquivo
        );
    }

    public function exportPdf(): Response
    {
        $dados = $this->montarDadosRelatorio(Auth::user());
        $arquivo = 'relatorio-financeiro-'.$dados['competencia'].'-'.Date::now()->format('His').'.pdf';

        return Pdf::loadView('relatorios.pdf', $dados)
            ->setPaper('a4', 'portrait')
            ->download($arquivo);
    }

    private function montarDadosRelatorio(User $user): array
    {
        $competencia = (string) request('competencia', now()->format('Y-m'));
        if (! preg_match('/^\d{4}-\d{2}$/', $competencia)) {
            $competencia = now()->format('Y-m');
        }

        $referencia = Carbon::createFromFormat('Y-m', $competencia)->startOfMonth();
        $mes = $referencia->month;
        $ano = $referencia->year;

        $despesasQuery = $user->despesas()
            ->whereYear('data', $ano)
            ->whereMonth('data', $mes);

        $receitaMensal = (float) $user->receita_liquida_mensal;
        $totalReceitas = $receitaMensal;
        $totalDespesas = (float) $despesasQuery->sum('valor');
        $saldo = $totalReceitas - $totalDespesas;

        $despesas = (clone $despesasQuery)->with('categoria')->orderByDesc('data')->get();

        $dataReceitaReferencia = $referencia->format('d/m/Y');
        $lancamentos = collect([
            [
                'tipo' => 'Receita',
                'data' => $dataReceitaReferencia,
                'titulo' => 'Receita liquida mensal',
                'categoria' => '-',
                'valor' => $receitaMensal,
            ],
        ])->concat(
            $despesas->map(fn ($despesa) => [
                'tipo' => 'Despesa',
                'data' => $despesa->data->format('d/m/Y'),
                'titulo' => $despesa->titulo,
                'categoria' => $despesa->categoria?->nome,
                'valor' => -((float) $despesa->valor),
            ])
        )->values();

        $mesesDisponiveis = collect(range(0, 23))
            ->map(fn (int $offset) => now()->startOfMonth()->subMonths($offset))
            ->map(fn (Carbon $data) => [
                'value' => $data->format('Y-m'),
                'label' => $data->format('m/Y'),
            ]);

        $competenciaLabel = $referencia->format('m/Y');

        return compact(
            'competencia',
            'competenciaLabel',
            'mes',
            'ano',
            'totalReceitas',
            'totalDespesas',
            'saldo',
            'lancamentos',
            'mesesDisponiveis',
        );
    }
}
