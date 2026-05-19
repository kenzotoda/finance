<div class="space-y-4">
    <div>
        <x-input-label for="titulo" value="Titulo" />
        <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" value="{{ old('titulo', $despesa->titulo ?? '') }}" required />
        <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <x-input-label for="valor" value="Valor total da compra" />
            <x-text-input id="valor" name="valor" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('valor', $despesa->valor ?? '') }}" required />
            <x-input-error :messages="$errors->get('valor')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="data" value="Data" />
            <x-text-input id="data" name="data" type="date" class="mt-1 block w-full" value="{{ old('data', isset($despesa) ? $despesa->data->format('Y-m-d') : '') }}" required />
            <x-input-error :messages="$errors->get('data')" class="mt-2" />
        </div>
    </div>
    @if ($permitirParcelamento ?? false)
        <div>
            <x-input-label for="quantidade_parcelas" value="Quantidade de parcelas" />
            <x-text-input id="quantidade_parcelas" name="quantidade_parcelas" type="number" min="1" max="48" class="mt-1 block w-full" value="{{ old('quantidade_parcelas', 1) }}" required />
            <p class="mt-1 text-xs text-gray-500">O valor total sera dividido automaticamente pelas parcelas (com ajuste de centavos).</p>
            <x-input-error :messages="$errors->get('quantidade_parcelas')" class="mt-2" />
        </div>
    @endif
    <div>
        <x-input-label for="categoria_id" value="Categoria" />
        <select id="categoria_id" name="categoria_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">Sem categoria</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" @selected(old('categoria_id', $despesa->categoria_id ?? '') == $categoria->id)>{{ $categoria->nome }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="descricao" value="Descricao (opcional)" />
        <textarea id="descricao" name="descricao" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('descricao', $despesa->descricao ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
    </div>
</div>
