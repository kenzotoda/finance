<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLucroFixoRequest;
use App\Http\Requests\UpdateLucroFixoRequest;
use App\Models\LucroFixo;
use App\Services\ReplicarLucrosFixosService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LucroFixoController extends Controller
{
    public function __construct(private readonly ReplicarLucrosFixosService $replicarLucrosFixosService) {}

    public function index(): View
    {
        $queryMensais = Auth::user()->lucrosFixos()
            ->where('periodicidade', LucroFixo::PERIODICIDADE_MENSAL);
        $queryAnuais = Auth::user()->lucrosFixos()
            ->where('periodicidade', LucroFixo::PERIODICIDADE_ANUAL);

        $lucrosFixosMensais = (clone $queryMensais)
            ->with('categoria')
            ->latest()
            ->get();

        $lucrosFixosAnuais = (clone $queryAnuais)
            ->with('categoria')
            ->latest()
            ->get();

        $totalLucrosFixosMensais = (clone $queryMensais)->sum('valor');
        $totalLucrosFixosAnuais = (clone $queryAnuais)->sum('valor');

        return view('lucros-fixos.index', compact(
            'lucrosFixosMensais',
            'lucrosFixosAnuais',
            'totalLucrosFixosMensais',
            'totalLucrosFixosAnuais',
        ));
    }

    public function create(): View
    {
        $categorias = Auth::user()->categorias()->orderBy('nome')->get();

        return view('lucros-fixos.create', compact('categorias'));
    }

    public function store(StoreLucroFixoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['ativa'] = $request->boolean('ativa');

        Auth::user()->lucrosFixos()->create($data);
        $this->replicarLucrosFixosService->execute(Auth::user());

        return to_route('lucros-fixos.index')->with('success', 'Lucro fixo cadastrado com sucesso.');
    }

    public function show(LucroFixo $lucroFixo): RedirectResponse
    {
        $this->garantirUsuario($lucroFixo);

        return to_route('lucros-fixos.edit', $lucroFixo);
    }

    public function edit(LucroFixo $lucroFixo): View
    {
        $this->garantirUsuario($lucroFixo);
        $categorias = Auth::user()->categorias()->orderBy('nome')->get();

        return view('lucros-fixos.edit', compact('lucroFixo', 'categorias'));
    }

    public function update(UpdateLucroFixoRequest $request, LucroFixo $lucroFixo): RedirectResponse
    {
        $this->garantirUsuario($lucroFixo);
        $data = $request->validated();
        $data['ativa'] = $request->boolean('ativa');

        $lucroFixo->update($data);
        $lucroFixo->receitasGeradas()->update([
            'titulo' => $lucroFixo->titulo,
            'valor' => $lucroFixo->valor,
            'categoria_id' => $lucroFixo->categoria_id,
            'descricao' => $lucroFixo->descricao,
        ]);
        $this->replicarLucrosFixosService->execute(Auth::user());

        return to_route('lucros-fixos.index')->with('success', 'Lucro fixo atualizado com sucesso.');
    }

    public function destroy(LucroFixo $lucroFixo): RedirectResponse
    {
        $this->garantirUsuario($lucroFixo);
        $lucroFixo->delete();

        return to_route('lucros-fixos.index')->with('success', 'Lucro fixo removido com sucesso.');
    }

    private function garantirUsuario(LucroFixo $lucroFixo): void
    {
        abort_unless($lucroFixo->user_id === Auth::id(), 403);
    }
}
