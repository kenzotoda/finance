<div class="space-y-4">
    <div>
        <x-input-label for="titulo" value="Titulo" />
        <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" value="{{ old('titulo', $receita->titulo ?? '') }}" required />
        <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <x-input-label for="valor" value="Valor" />
            <x-text-input id="valor" name="valor" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('valor', $receita->valor ?? '') }}" required />
            <x-input-error :messages="$errors->get('valor')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="data" value="Data" />
            <x-text-input id="data" name="data" type="date" class="mt-1 block w-full" value="{{ old('data', isset($receita) ? $receita->data->format('Y-m-d') : '') }}" required />
            <x-input-error :messages="$errors->get('data')" class="mt-2" />
        </div>
    </div>
    <div>
        <x-input-label for="categoria_id" value="Categoria (opcional)" />
        <select id="categoria_id" name="categoria_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">Sem categoria</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" @selected(old('categoria_id', $receita->categoria_id ?? '') == $categoria->id)>{{ $categoria->nome }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="descricao" value="Descricao (opcional)" />
        <textarea id="descricao" name="descricao" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('descricao', $receita->descricao ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
    </div>
</div>
