<div class="space-y-4">
    @if (! isset($contaPagarReceber))
        <div>
            <x-input-label for="tipo" value="Tipo" />
            <select id="tipo" name="tipo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                <option value="pagar" @selected(old('tipo', $tipo ?? 'pagar') === 'pagar')>Pagar</option>
                <option value="receber" @selected(old('tipo', $tipo ?? 'pagar') === 'receber')>Receber</option>
            </select>
            <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
        </div>
    @else
        <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
            Tipo:
            <span class="font-semibold {{ $contaPagarReceber->tipo === 'pagar' ? 'text-red-700' : 'text-emerald-700' }}">
                {{ $contaPagarReceber->tipo === 'pagar' ? 'Pagar' : 'Receber' }}
            </span>
            @if ($contaPagarReceber->isParcelada())
                <span class="ml-2 text-gray-500">
                    Parcela {{ $contaPagarReceber->parcela_atual }}/{{ $contaPagarReceber->total_parcelas }}
                </span>
            @endif
        </div>
    @endif

    <div>
        <x-input-label for="titulo" value="Titulo" />
        <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" value="{{ old('titulo', $contaPagarReceber->titulo ?? '') }}" required />
        <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <x-input-label for="valor" value="{{ isset($contaPagarReceber) ? 'Valor desta parcela' : 'Valor total' }}" />
            <x-text-input id="valor" name="valor" type="number" step="0.01" min="0.01" class="mt-1 block w-full" value="{{ old('valor', $contaPagarReceber->valor ?? '') }}" required />
            @unless (isset($contaPagarReceber))
                <p class="mt-1 text-xs text-gray-500">Se parcelado, o valor total sera dividido entre as parcelas.</p>
            @endunless
            <x-input-error :messages="$errors->get('valor')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="data" value="{{ isset($contaPagarReceber) ? 'Data' : 'Data da 1a parcela' }}" />
            <x-text-input id="data" name="data" type="date" class="mt-1 block w-full" value="{{ old('data', isset($contaPagarReceber) ? $contaPagarReceber->data->format('Y-m-d') : now()->format('Y-m-d')) }}" required />
            <x-input-error :messages="$errors->get('data')" class="mt-2" />
        </div>
        @unless (isset($contaPagarReceber))
            <div>
                <x-input-label for="quantidade_parcelas" value="Parcelas" />
                <x-text-input id="quantidade_parcelas" name="quantidade_parcelas" type="number" min="1" max="48" class="mt-1 block w-full" value="{{ old('quantidade_parcelas', 1) }}" required />
                <x-input-error :messages="$errors->get('quantidade_parcelas')" class="mt-2" />
            </div>
        @endunless
    </div>

    <div>
        <x-input-label for="categoria_id" value="Categoria (opcional)" />
        <select id="categoria_id" name="categoria_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">Sem categoria</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" @selected(old('categoria_id', $contaPagarReceber->categoria_id ?? '') == $categoria->id)>{{ $categoria->nome }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="descricao" value="Descricao (opcional)" />
        <textarea id="descricao" name="descricao" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('descricao', $contaPagarReceber->descricao ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
    </div>
</div>
