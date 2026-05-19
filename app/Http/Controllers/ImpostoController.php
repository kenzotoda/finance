<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImpostoRequest;
use App\Http\Requests\UpdateImpostoRequest;
use App\Models\Imposto;
use App\Services\ReplicarImpostosService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ImpostoController extends Controller
{
    public function __construct(private readonly ReplicarImpostosService $replicarImpostosService) {}

    public function index(): View
    {
        $queryImpostos = Auth::user()->impostos();

        $impostos = (clone $queryImpostos)
            ->with('categoria')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalImpostos = (clone $queryImpostos)->sum('valor');

        return view('impostos.index', compact('impostos', 'totalImpostos'));
    }

    public function create(): View
    {
        $categorias = Auth::user()->categorias()->orderBy('nome')->get();
        $tiposImposto = Imposto::TIPOS;

        return view('impostos.create', compact('categorias', 'tiposImposto'));
    }

    public function store(StoreImpostoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['ativa'] = $request->boolean('ativa');

        Auth::user()->impostos()->create($data);
        $this->replicarImpostosService->execute(Auth::user());

        return to_route('impostos.index')->with('success', 'Imposto cadastrado com sucesso.');
    }

    public function show(Imposto $imposto): RedirectResponse
    {
        $this->garantirUsuario($imposto);

        return to_route('impostos.edit', $imposto);
    }

    public function edit(Imposto $imposto): View
    {
        $this->garantirUsuario($imposto);
        $categorias = Auth::user()->categorias()->orderBy('nome')->get();
        $tiposImposto = Imposto::TIPOS;

        return view('impostos.edit', compact('imposto', 'categorias', 'tiposImposto'));
    }

    public function update(UpdateImpostoRequest $request, Imposto $imposto): RedirectResponse
    {
        $this->garantirUsuario($imposto);
        $data = $request->validated();
        $data['ativa'] = $request->boolean('ativa');

        $imposto->update($data);
        $imposto->despesasGeradas()->update([
            'titulo' => $imposto->titulo,
            'valor' => $imposto->valor,
            'categoria_id' => $imposto->categoria_id,
            'descricao' => $imposto->descricao,
        ]);
        $this->replicarImpostosService->execute(Auth::user());

        return to_route('impostos.index')->with('success', 'Imposto atualizado com sucesso.');
    }

    public function destroy(Imposto $imposto): RedirectResponse
    {
        $this->garantirUsuario($imposto);
        $imposto->delete();

        return to_route('impostos.index')->with('success', 'Imposto removido com sucesso.');
    }

    private function garantirUsuario(Imposto $imposto): void
    {
        abort_unless($imposto->user_id === Auth::id(), 403);
    }
}
