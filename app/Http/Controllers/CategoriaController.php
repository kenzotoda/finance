<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    public function index(): View
    {
        $categorias = Auth::user()
            ->categorias()
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('categorias.index', compact('categorias'));
    }

    public function create(): View
    {
        return view('categorias.create');
    }

    public function store(StoreCategoriaRequest $request): RedirectResponse
    {
        Auth::user()->categorias()->create($request->validated());

        return to_route('categorias.index')->with('success', 'Categoria criada com sucesso.');
    }

    public function show(Categoria $categoria): RedirectResponse
    {
        $this->garantirUsuario($categoria);

        return to_route('categorias.edit', $categoria);
    }

    public function edit(Categoria $categoria): View
    {
        $this->garantirUsuario($categoria);

        return view('categorias.edit', compact('categoria'));
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria): RedirectResponse
    {
        $this->garantirUsuario($categoria);

        $categoria->update($request->validated());

        return to_route('categorias.index')->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(Categoria $categoria): RedirectResponse
    {
        $this->garantirUsuario($categoria);

        $categoria->delete();

        return to_route('categorias.index')->with('success', 'Categoria removida com sucesso.');
    }

    private function garantirUsuario(Categoria $categoria): void
    {
        abort_unless($categoria->user_id === Auth::id(), 403);
    }
}
