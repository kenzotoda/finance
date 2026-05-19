<?php

namespace App\Http\Controllers;

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

        $totalDespesasFixas = $user->despesasFixas()->where('ativa', true)->sum('valor');
        $totalImpostos = $user->impostos()->where('ativa', true)->sum('valor');
        $totalLucrosFixos = $user->lucrosFixos()->where('ativa', true)->sum('valor');
        $totalSaidas = $totalDespesasFixas + $totalImpostos;
        $saldo = $totalLucrosFixos - $totalSaidas;

        return view('dashboard', compact(
            'totalDespesasFixas',
            'totalImpostos',
            'totalLucrosFixos',
            'totalSaidas',
            'saldo',
        ));
    }
}
