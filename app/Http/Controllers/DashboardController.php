<?php

namespace App\Http\Controllers;

use App\Models\DespesaFixa;
use App\Models\Imposto;
use App\Models\LucroFixo;
use App\Services\ReplicarDespesasFixasService;
use App\Services\ReplicarImpostosService;
use App\Services\ReplicarLucrosFixosService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ReplicarDespesasFixasService $replicarDespesasFixasService,
        private readonly ReplicarLucrosFixosService $replicarLucrosFixosService,
        private readonly ReplicarImpostosService $replicarImpostosService,
    ) {}

    public function index(): View
    {
        $user = Auth::user();
        $this->replicarDespesasFixasService->execute($user);
        $this->replicarLucrosFixosService->execute($user);
        $this->replicarImpostosService->execute($user);

        $totalDespesasFixasMensais = $user->despesasFixas()
            ->where('ativa', true)
            ->where('periodicidade', DespesaFixa::PERIODICIDADE_MENSAL)
            ->sum('valor');
        $totalImpostosMensais = $user->impostos()
            ->where('ativa', true)
            ->where('periodicidade', Imposto::PERIODICIDADE_MENSAL)
            ->sum('valor');
        $totalLucrosFixosMensais = $user->lucrosFixos()
            ->where('ativa', true)
            ->where('periodicidade', LucroFixo::PERIODICIDADE_MENSAL)
            ->sum('valor');
        $totalSaidasMensais = $totalDespesasFixasMensais + $totalImpostosMensais;
        $saldoMensal = $totalLucrosFixosMensais - $totalSaidasMensais;

        return view('dashboard', compact(
            'totalDespesasFixasMensais',
            'totalImpostosMensais',
            'totalLucrosFixosMensais',
            'saldoMensal',
        ));
    }
}
