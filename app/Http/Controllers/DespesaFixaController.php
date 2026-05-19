<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDespesaFixaRequest;
use App\Http\Requests\UpdateDespesaFixaRequest;
use App\Models\DespesaFixa;
use App\Services\ReplicarDespesasFixasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DespesaFixaController extends Controller
{
    public function __construct(private readonly ReplicarDespesasFixasService $replicarDespesasFixasService) {}

    public function index(): View
    {
        $queryDespesasFixas = Auth::user()->despesasFixas();

        $despesasFixas = (clone $queryDespesasFixas)
            ->with('categoria')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalDespesasFixas = (clone $queryDespesasFixas)->sum('valor');

        return view('despesas-fixas.index', compact('despesasFixas', 'totalDespesasFixas'));
    }

    public function create(): View
    {
        $categorias = Auth::user()->categorias()->orderBy('nome')->get();

        return view('despesas-fixas.create', compact('categorias'));
    }

    public function store(StoreDespesaFixaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['ativa'] = $request->boolean('ativa');

        Auth::user()->despesasFixas()->create($data);
        $this->replicarDespesasFixasService->execute(Auth::user());

        return to_route('despesas-fixas.index')->with('success', 'Despesa fixa cadastrada com sucesso.');
    }

    public function show(DespesaFixa $despesaFixa): RedirectResponse
    {
        $this->garantirUsuario($despesaFixa);

        return to_route('despesas-fixas.edit', $despesaFixa);
    }

    public function edit(DespesaFixa $despesaFixa): View
    {
        $this->garantirUsuario($despesaFixa);
        $categorias = Auth::user()->categorias()->orderBy('nome')->get();

        return view('despesas-fixas.edit', compact('despesaFixa', 'categorias'));
    }

    public function update(UpdateDespesaFixaRequest $request, DespesaFixa $despesaFixa): RedirectResponse
    {
        $this->garantirUsuario($despesaFixa);
        $data = $request->validated();
        $data['ativa'] = $request->boolean('ativa');

        $despesaFixa->update($data);
        $despesaFixa->despesasGeradas()->update([
            'titulo' => $despesaFixa->titulo,
            'valor' => $despesaFixa->valor,
            'categoria_id' => $despesaFixa->categoria_id,
            'descricao' => $despesaFixa->descricao,
        ]);
        $this->replicarDespesasFixasService->execute(Auth::user());

        return to_route('despesas-fixas.index')->with('success', 'Despesa fixa atualizada com sucesso.');
    }

    public function destroy(DespesaFixa $despesaFixa): RedirectResponse
    {
        $this->garantirUsuario($despesaFixa);
        $despesaFixa->delete();

        return to_route('despesas-fixas.index')->with('success', 'Despesa fixa removida com sucesso.');
    }

    private function garantirUsuario(DespesaFixa $despesaFixa): void
    {
        abort_unless($despesaFixa->user_id === Auth::id(), 403);
    }
}
