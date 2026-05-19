<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReceitaRequest;
use App\Http\Requests\UpdateReceitaRequest;
use App\Models\Receita;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReceitaController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $mes = (int) request('mes', now()->month);
        $ano = (int) request('ano', now()->year);
        $categoriaId = request('categoria_id');

        $receitas = $user->receitas()
            ->with('categoria')
            ->when($mes, fn (Builder $query) => $query->whereMonth('data', $mes))
            ->when($ano, fn (Builder $query) => $query->whereYear('data', $ano))
            ->when($categoriaId, fn (Builder $query) => $query->where('categoria_id', $categoriaId))
            ->orderByDesc('data')
            ->paginate(10)
            ->withQueryString();

        $categorias = $user->categorias()->orderBy('nome')->get();

        return view('receitas.index', compact('receitas', 'categorias', 'mes', 'ano', 'categoriaId'));
    }

    public function create(): View
    {
        $categorias = Auth::user()->categorias()->orderBy('nome')->get();

        return view('receitas.create', compact('categorias'));
    }

    public function store(StoreReceitaRequest $request): RedirectResponse
    {
        Auth::user()->receitas()->create($request->validated());

        return to_route('receitas.index')->with('success', 'Receita cadastrada com sucesso.');
    }

    public function show(Receita $receita): RedirectResponse
    {
        $this->garantirUsuario($receita);

        return to_route('receitas.edit', $receita);
    }

    public function edit(Receita $receita): View
    {
        $this->garantirUsuario($receita);

        $categorias = Auth::user()->categorias()->orderBy('nome')->get();

        return view('receitas.edit', compact('receita', 'categorias'));
    }

    public function update(UpdateReceitaRequest $request, Receita $receita): RedirectResponse
    {
        $this->garantirUsuario($receita);

        $receita->update($request->validated());

        return to_route('receitas.index')->with('success', 'Receita atualizada com sucesso.');
    }

    public function destroy(Receita $receita): RedirectResponse
    {
        $this->garantirUsuario($receita);

        $receita->delete();

        return to_route('receitas.index')->with('success', 'Receita removida com sucesso.');
    }

    private function garantirUsuario(Receita $receita): void
    {
        abort_unless($receita->user_id === Auth::id(), 403);
    }
}
